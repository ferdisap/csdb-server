interface UploadConfig {
  driver: string;
  environment: string;
  max_size: number;
  chunk_size: number;
  expiration: number;
}

interface ChunkMetadata {
  uploadId: string;
  fileName: string;
  fileSize: number;
  chunkSize: number;
  totalChunks: number;
  uploadedChunks: number;
  createdAt: number;
}

async function hashString(message:string) {
  const msgBuffer = new TextEncoder().encode(message);
  const hashBuffer = await crypto.subtle.digest('SHA-256', msgBuffer);
  const hashArray = Array.from(new Uint8Array(hashBuffer));
  const hashHex = hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
  return hashHex;
}

export class ChunkedUploadManager {
  private config: UploadConfig;
  private endpoint: string;
  private uploadId: string | null = null;
  private abortController: AbortController | null = null;
  private metadata: ChunkMetadata | null = null;

  constructor(endpoint: string = '/dochub/upload') {
    this.endpoint = endpoint;
    this.config = {
      driver: 'native',
      environment: 'development',
      max_size: 5 * 1024 * 1024 * 1024, // 5 GB
      chunk_size: 1 * 1024 * 1024, // 1 MB
      expiration: 604800,
    };
  }

  /**
   * Inisialisasi dengan konfigurasi dari server
   */
  async initialize(): Promise<UploadConfig> {
    try {
      const response = await fetch('/dochub/upload/config');
      if (!response.ok) throw new Error(`HTTP ${response.status}`);

      const config = await response.json();
      this.config = {
        ...this.config,
        ...config,
        chunk_size: config.chunk_size || this.config.chunk_size,
        max_size: config.max_size || this.config.max_size,
      };

      return this.config;
    } catch (error) {
      console.warn('Failed to fetch upload config, using defaults:', error);
      return this.config;
    }
  }

  /**
   * Mulai upload file dengan chunking
   */
  async upload(file: File): Promise<string> {
    // Validasi
    if (file.size > this.config.max_size) {
      throw new Error(`File too large. Max: ${this.formatBytes(this.config.max_size)}`);
    }

    // Generate upload ID
    // this.uploadId = `native_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
    const hashname = await hashString(file.name);
    this.uploadId = `native_${hashname}`;

    // Hitung chunk
    const chunkSize = this.config.chunk_size;
    const totalChunks = Math.ceil(file.size / chunkSize);

    this.metadata = {
      uploadId: this.uploadId,
      fileName: file.name,
      fileSize: file.size,
      chunkSize: chunkSize,
      totalChunks: totalChunks,
      uploadedChunks: 0,
      createdAt: Date.now(),
    };

    console.log('Starting upload:', {
      uploadId: this.uploadId,
      fileName: file.name,
      fileSize: this.formatBytes(file.size),
      totalChunks: totalChunks,
      chunkSize: this.formatBytes(chunkSize)
    });

    // Upload chunk per chunk
    for (let i = 0; i < totalChunks; i++) {
      await this.uploadChunk(file, i, totalChunks);
    }

    // Trigger processing
    await this.triggerProcessing();

    return this.uploadId;
  }

  /**
   * Upload satu chunk
   */
  private async uploadChunk(file: File, chunkIndex: number, totalChunks: number): Promise<void> {
    this.abortController = new AbortController();

    const start = chunkIndex * this.metadata!.chunkSize;
    const end = Math.min(start + this.metadata!.chunkSize, this.metadata!.fileSize);

    // 🔑 Baca chunk sebagai Blob
    const chunk = file.slice(start, end);

    try {
      // 🔑 Pakai fetch dengan Blob + custom headers
      const response = await fetch(`${this.endpoint}/chunk`, {
        method: 'POST',
        headers: {
          'X-Upload-ID': this.uploadId!,
          'X-Chunk-Index': chunkIndex.toString(),
          'X-Total-Chunks': totalChunks.toString(),
          'X-File-Name': this.metadata!.fileName,
          'X-File-Size': this.metadata!.fileSize.toString(),
          'Content-Type': 'application/octet-stream',
          'X-CSRF-TOKEN': this.getCSRFToken(),
        },
        body: chunk, // 🔑 Langsung kirim Blob
        signal: this.abortController.signal,
      });

      if (!response.ok) {
        const errorText = await response.text();
        throw new Error(`Chunk ${chunkIndex} failed: ${response.status} ${errorText}`);
      }

      const result = await response.json();
      this.metadata!.uploadedChunks = chunkIndex + 1;

      console.log(`Chunk ${chunkIndex + 1}/${totalChunks} uploaded`, {
        size: this.formatBytes(result.size),
        progress: `${Math.round(((chunkIndex + 1) / totalChunks) * 100)}%`
      });

    } catch (error) {
      if (error.name === 'AbortError') {
        throw new Error('Upload cancelled by user');
      }
      throw error;
    }
  }
  // private async uploadChunk(file: File, chunkIndex: number, totalChunks: number): Promise<void> {
  //   this.abortController = new AbortController();

  //   const start = chunkIndex * this.metadata!.chunkSize;
  //   const end = Math.min(start + this.metadata!.chunkSize, this.metadata!.fileSize);
  //   const chunk = file.slice(start, end);

  //   const formData = new FormData();
  //   formData.append('upload_id', this.uploadId!);
  //   formData.append('chunk_index', chunkIndex.toString());
  //   formData.append('total_chunks', totalChunks.toString());
  //   formData.append('file_name', this.metadata!.fileName);
  //   formData.append('file_size', this.metadata!.fileSize.toString());
  //   formData.append('chunk', chunk, `${this.metadata!.fileName}.part${chunkIndex}`);

  //   try {
  //     const response = await fetch(`${this.endpoint}/chunk`, {
  //       method: 'POST',
  //       body: formData,
  //       signal: this.abortController.signal,
  //     });

  //     if (!response.ok) {
  //       const errorText = await response.text();
  //       throw new Error(`Chunk ${chunkIndex} failed: ${response.status} ${errorText}`);
  //     }

  //     const result = await response.json();
  //     this.metadata!.uploadedChunks = chunkIndex + 1;

  //     console.log(`Chunk ${chunkIndex + 1}/${totalChunks} uploaded`, {
  //       bytes: this.formatBytes(end - start),
  //       progress: `${Math.round(((chunkIndex + 1) / totalChunks) * 100)}%`
  //     });

  //     // Callback progress
  //     this.onProgress?.({
  //       uploadId: this.uploadId!,
  //       fileName: this.metadata!.fileName,
  //       progress: (chunkIndex + 1) / totalChunks,
  //       uploaded: end,
  //       total: this.metadata!.fileSize,
  //       chunkIndex: chunkIndex,
  //       totalChunks: totalChunks,
  //     });

  //   } catch (error) {
  //     if (error.name === 'AbortError') {
  //       throw new Error('Upload cancelled by user');
  //     }
  //     throw error;
  //   }
  // }

  /**
   * Trigger pemrosesan setelah semua chunk selesai
   */
  private async triggerProcessing(): Promise<void> {
    if (!this.uploadId) throw new Error('No upload ID');

    const response = await fetch(`${this.endpoint}/process`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': this.getCSRFToken(),
      },
      body: JSON.stringify({
        upload_id: this.uploadId,
        file_name: this.metadata!.fileName,
      }),
    });

    if (!response.ok) {
      throw new Error(`Processing failed: ${response.status}`);
    }
  }

  /**
   * Cek status upload
   */
  async getStatus(uploadId: string): Promise<any> {
    const response = await fetch(`${this.endpoint}/${uploadId}/status`);
    if (!response.ok) {
      throw new Error(`Status check failed: ${response.status}`);
    }
    return response.json();
  }

  /**
   * Batalkan upload
   */
  cancel(): void {
    if (this.abortController) {
      this.abortController.abort();
      this.abortController = null;
    }
    console.log('Upload cancelled');
  }

  // Callbacks
  onProgress?: (data: {
    uploadId: string;
    fileName: string;
    progress: number;
    uploaded: number;
    total: number;
    chunkIndex: number;
    totalChunks: number;
  }) => void;

  onError?: (error: Error) => void;

  // Helper
  private getCSRFToken(): string {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') || '' : '';
  }

  private formatBytes(bytes: number): string {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
  }
}