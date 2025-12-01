<template>
    <div class="chunked-upload">
        <div class="upload-zone" @click="openFilePicker">
            <input
                type="file"
                ref="fileInput"
                @change="handleFileSelect"
                accept=".zip,.tar,.gz"
                class="hidden"
            />
            <div v-if="!uploading">
                <svg>...</svg>
                <p>Drag & drop or click to upload</p>
                <p class="hint">Supports chunked upload for large files</p>
            </div>

            <div v-else class="progress-container">
                <div class="progress-bar">
                    <div
                        class="progress-fill"
                        :style="{ width: `${progress}%` }"
                    ></div>
                </div>
                <div class="progress-info">
                    <span>{{ progress }}%</span>
                    <span v-if="speed">{{ formatBytes(speed) }}/s</span>
                    <button @click="cancel" class="cancel-btn">Cancel</button>
                </div>
            </div>
        </div>

        <div v-if="status" class="status">
            <h4>Status: {{ status.status }}</h4>
            <div v-if="status.progress < 100">
                <p>
                    Chunks: {{ status.uploaded_chunks }} /
                    {{ status.total_chunks }}
                </p>
                <button @click="checkStatus" class="btn">Refresh</button>
            </div>
            <div v-else-if="status.job_id">
                <p>Processing ZIP... Job ID: {{ status.job_id }}</p>
                <button @click="checkStatus" class="btn">Check Status</button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue";
import { ChunkedUploadManager } from "./ChunkUploadManager";

const fileInput = ref(null);
const uploading = ref(false);
const progress = ref(0);
const speed = ref(0);
const status = ref(null);
const uploadManager = new ChunkedUploadManager();

onMounted(async () => {
    await uploadManager.initialize();
});

const openFilePicker = () => {
    fileInput.value.click();
};

const handleFileSelect = async (event) => {
    const files = event.target.files
    if (files.length === 0) return
    const file = files[0]
    await startUpload(file)
    event.target.value = ''
};

const startUpload = async (file) => {
    uploading.value = true;
    progress.value = 0;
    speed.value = 0;
    status.value = null;

    uploadManager.onProgress = (data) => {
        progress.value = Math.round(data.progress * 100);
        // Hitung speed
        if (data.chunkIndex > 0) {
            const chunkSize =
                data.uploaded -
                data.chunkIndex * uploadManager.config.chunk_size;
            speed.value = chunkSize / 1000; // KB/s
        }
    };

    try {
        const uploadId = await uploadManager.upload(file);
        await pollStatus(uploadId);
    } catch (error) {
        console.error("Upload failed:", error);
        alert(`Upload failed: ${error.message}`);
    } finally {
        uploading.value = false;
    }
};

const pollStatus = async (uploadId) => {
    const interval = setInterval(async () => {
        try {
            const stat = await uploadManager.getStatus(uploadId);
            status.value = stat;

            if (stat.status === "completed" || stat.error) {
                clearInterval(interval);
            }
        } catch (error) {
            console.error("Status check failed:", error);
            clearInterval(interval);
        }
    }, 2000);
};

const cancel = () => {
    uploadManager.cancel();
    uploading.value = false;
};

const checkStatus = async () => {
    if (status.value?.id) {
        status.value = await uploadManager.getStatus(status.value.id);
    }
};

const formatBytes = (bytes) => {
    if (bytes === 0) return "0 Bytes";
    const k = 1024;
    const sizes = ["Bytes", "KB", "MB", "GB"];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i];
};
</script>

<style scoped>
.upload-manager {
  max-width: 600px;
  margin: 2rem auto;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.env-badge {
  background: #f0f0f0;
  border-radius: 4px;
  padding: 8px 12px;
  margin-bottom: 1rem;
  font-size: 0.875rem;
  display: flex;
  gap: 1rem;
  justify-content: center;
}

.env-shared { background: #fff3cd; border-left: 4px solid #ffc107; }
.env-dedicated { background: #d4edda; border-left: 4px solid #28a745; }
.env-container { background: #cce5ff; border-left: 4px solid #007bff; }

.upload-zone {
  border: 2px dashed #ccc;
  border-radius: 8px;
  padding: 3rem 2rem;
  text-align: center;
  cursor: pointer;
  transition: all 0.3s ease;
  background: #fafafa;
}

.upload-zone:hover {
  border-color: #007bff;
  background: #f8f9fa;
}

.upload-zone svg {
  stroke: #6c757d;
  margin-bottom: 1rem;
}

.upload-zone p {
  margin: 0.5rem 0;
  color: #495057;
}

.hint {
  font-size: 0.875rem;
  color: #6c757d;
}

.limits {
  font-size: 0.75rem;
  color: #6c757d;
  margin-top: 1rem;
}

.progress-container {
  text-align: left;
}

.progress-bar {
  height: 8px;
  background: #e9ecef;
  border-radius: 4px;
  margin-bottom: 1rem;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, #007bff, #0056b3);
  transition: width 0.3s ease;
}

.progress-info {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 0.875rem;
}

.control-btn {
  padding: 4px 8px;
  background: #6c757d;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 0.75rem;
}

.control-btn:hover {
  background: #545b62;
}

.control-btn.cancel {
  background: #dc3545;
}

.control-btn.cancel:hover {
  background: #c82333;
}

.result {
  margin-top: 1.5rem;
  padding: 1rem;
  border-radius: 4px;
}

.result.success {
  background: #d4edda;
  border: 1px solid #c3e6cb;
  color: #155724;
}

.result.error {
  background: #f8d7da;
  border: 1px solid #f5c6cb;
  color: #721c24;
}

.result-icon {
  float: left;
  margin-right: 1rem;
}

.result-icon svg {
  width: 24px;
  height: 24px;
  stroke-width: 2;
}

.result.success .result-icon svg {
  stroke: #28a745;
}

.result.error .result-icon svg {
  stroke: #dc3545;
}

.result-content h3 {
  margin: 0 0 0.5rem 2rem;
  font-size: 1.125rem;
}

.result-content p {
  margin: 0 0 0.5rem 2rem;
  font-size: 0.875rem;
}

.job-info {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-top: 0.5rem;
  font-size: 0.875rem;
}

.btn-sm {
  padding: 2px 6px;
  font-size: 0.75rem;
  background: #007bff;
  color: white;
  border: none;
  border-radius: 3px;
  cursor: pointer;
}

.hidden {
  display: none;
}
</style>