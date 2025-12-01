import { toast, Toaster } from "vue-sonner"

export const ToastStyle = {
  warning: {
    style: {
      background: '#fef08a', // kuning lembut (tailwind: yellow-200)
      color: '#713f12', // teks coklat gelap (tailwind: yellow-900)
      border: '1px solid #facc15', // border kuning (tailwind: yellow-400)
    },
    iconTheme: {
      primary: '#facc15',
      secondary: '#fefce8',
    },
  },
  danger: {
    style: {
      background: '#f97474ff', // merah muda lembut (Tailwind: red-200)
      color: '#000000ff',      // teks merah tua (Tailwind: red-900)
      border: '1px solid #f81616ff', // border merah (Tailwind: red-400)
    },
    iconTheme: {
      primary: '#ef4444',    // warna utama ikon merah
      secondary: '#fef2f2',  // latar belakang ikon merah muda
    },
  },
  info: {
    style: {
      background: '#dcfce7', // hijau muda (Tailwind: green-100)
      color: '#14532d',      // teks hijau tua (Tailwind: green-900)
      border: '1px solid #4ade80', // border hijau (Tailwind: green-400)
    },
    iconTheme: {
      primary: '#22c55e',    // warna utama ikon (Tailwind: green-500)
      secondary: '#f0fdf4',  // latar belakang ikon
    },
  },
  success: {
    style: {
      background: '#dbeafe', // biru muda (Tailwind: blue-100)
      color: '#1e3a8a',      // teks biru tua (Tailwind: blue-900)
      border: '1px solid #60a5fa', // border biru (Tailwind: blue-400)
    },
    iconTheme: {
      primary: '#3b82f6',    // warna ikon utama (Tailwind: blue-500)
      secondary: '#eff6ff',  // latar belakang ikon
    },
  },
  fail: {
    style: {
      background: '#fef2f2',   // merah muda lembut (Tailwind: red-50)
      color: '#7f1d1d',        // teks merah tua (Tailwind: red-900)
      border: '1px solid #fca5a5', // border merah muda (Tailwind: red-300)
    },
    iconTheme: {
      primary: '#dc2626',      // merah utama (Tailwind: red-600)
      secondary: '#fee2e2',    // latar belakang ikon merah muda
    },
  }
}

interface ToastConfigInterface {
  style?: {
    background: string,
    color: string,
    border: string,
  },
  id?: string | number,
  description?: string,
}

export function warning(message: string, description: string | null = null, id: string | number | null = null) {
  const config: ToastConfigInterface = {
    style: ToastStyle.warning.style,
  };
  if (id) {
    config.id = id;
  }
  if (description) {
    config.description = description
  }
  return toast.warning(message, config)
}

export function danger(message: string, description: string | null = null, id: string | number | null = null) {
  const config: ToastConfigInterface = {
    style: ToastStyle.info.style
  };
  if (id) {
    config.id = id;
  }
  if (description) {
    config.description = description
  }
  return toast.info(message, config)
}

export function info(message: string, description: string | null = null, id: string | number | null = null) {
  const config: ToastConfigInterface = {
    style: ToastStyle.info.style
  };
  if (id) {
    config.id = id;
  }
  if (description) {
    config.description = description
  }
  return toast.info(message, config)
}

export function success(message: string, description: string | null = null, id: string | number | null = null) {
  const config: ToastConfigInterface = {
    style: ToastStyle.success.style
  };
  if (id) {
    config.id = id;
  }
  if (description) {
    config.description = description
  }
  return toast.success(message, config)
}

export function fail(message: string, description: string | null = null, id: string | number | null = null) {
  const config: ToastConfigInterface = {
    style: ToastStyle.fail.style
  };
  if (id) {
    config.id = id;
  }
  if (description) {
    config.description = description
  }
  return toast.error(message, config)
}

export function message(message: string, description: string, id: string | number | null = null) {
  const config: ToastConfigInterface = {
    description: description
  };
  if (id) {
    config.id = id;
  }
  return toast.message(message, config)
}

export function undo(callback:Function, message: string, description: string | null = null, id: string | number | null = null) {
  const config: ToastConfigInterface = {};
  if (id) {
    config.id = id;
  }
  if (description) {
    config.description = description
  }
  return toast(message, {
    action: {
      label: 'Undo',
      onClick: callback
    },
  })
}

export function loading(message: string, description: string | null = null, id: string | number | null = null) {
  const config: ToastConfigInterface = {};
  if (id) {
    config.id = id;
  }
  if (description) {
    config.description = description
  }
  return toast.loading(message, config)
}

export { Toaster };