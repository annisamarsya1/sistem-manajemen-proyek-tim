/**
 * Class custom untuk menangani error dari API.
 * Membawa status HTTP dan data tambahan untuk mempermudah debugging dan penanganan UI.
 */
export class ApiError extends Error {
  public status: number;
  public data: any;

  constructor(message: string, status: number, data: any = null) {
    super(message);
    this.name = 'ApiError';
    this.status = status;
    this.data = data;
  }
}

interface FetchOptions extends RequestInit {
  data?: any;
}

// Menyiapkan baseURL untuk request, mengambil dari env atau fallback ke localhost
const baseURL = (import.meta.env.VITE_API_URL as string) || 'http://localhost:8000/api';

/**
 * Fungsi utilitas utama untuk mengambil data dari API (wrapper dari fetch).
 * Menangani pengaturan header, konversi body JSON, kredensial Sanctum, dan parse error.
 * 
 * @param endpoint Endpoint API tujuan (relatif terhadap baseURL)
 * @param options Konfigurasi tambahan (headers, body, method, dll)
 * @returns Promise berisi data JSON hasil response
 */
export const fetchData = async <T>(endpoint: string, options: FetchOptions = {}): Promise<T> => {
  const url = `${baseURL.replace(/\/$/, '')}/${endpoint.replace(/^\//, '')}`;
  
  const headers = new Headers(options.headers || {});
  if (!headers.has('Accept')) {
    headers.set('Accept', 'application/json');
  }
  if (!headers.has('Content-Type')) {
    headers.set('Content-Type', 'application/json');
  }

  const fetchConfig: RequestInit = {
    ...options,
    headers,
    cache: 'no-store', // Memastikan data realtime
  };

  // Convert data object to JSON body if provided and it's not a GET request
  if (options.data && fetchConfig.method && fetchConfig.method.toUpperCase() !== 'GET') {
    fetchConfig.body = JSON.stringify(options.data);
  }

  // Handle credentials explicitly as required by Laravel Sanctum
  fetchConfig.credentials = 'include';

  let response: Response;
  try {
    response = await fetch(url, fetchConfig);
  } catch (error) {
    // Catch network connectivity errors
    throw new ApiError('Terjadi kesalahan jaringan. Gagal terhubung ke server.', 0);
  }

  // Handle empty responses
  if (response.status === 204 || response.headers.get('content-length') === '0') {
    if (!response.ok) {
       throw new ApiError(`Request gagal dengan status ${response.status}`, response.status);
    }
    return {} as T;
  }

  // Validate JSON format
  let jsonResponse: any;
  try {
    jsonResponse = await response.json();
  } catch (error) {
    if (!response.ok) {
      throw new ApiError(`Request gagal dengan status ${response.status}`, response.status);
    }
    throw new ApiError('Format respons dari server tidak valid (bukan JSON).', response.status);
  }

  // If status is not OK, throw ApiError with structured data
  if (!response.ok) {
    const message = jsonResponse.message || `Request gagal dengan status ${response.status}`;
    throw new ApiError(message, response.status, jsonResponse);
  }

  // Validate response structure (direct array or wrapped in json.data)
  if (Array.isArray(jsonResponse)) {
    return jsonResponse as T;
  }
  
  // If it's an object with a data key that contains the actual payload, return it, otherwise return the whole object
  if (jsonResponse && typeof jsonResponse === 'object' && 'data' in jsonResponse) {
     return jsonResponse.data as T;
  }

  return jsonResponse as T;
};

// Also export a configured function to fetch raw URLs (like the CSRF endpoint)
/**
 * Fungsi utilitas untuk melakukan request ke URL penuh tanpa baseURL dan parsing otomatis.
 * Berguna untuk request khusus seperti memanggil endpoint CSRF cookie.
 */
export const fetchRawUrl = async (fullUrl: string, options: FetchOptions = {}): Promise<any> => {
  const headers = new Headers(options.headers || {});
  if (!headers.has('Accept')) headers.set('Accept', 'application/json');
  if (!headers.has('Content-Type')) headers.set('Content-Type', 'application/json');

  let response: Response;
  try {
    response = await fetch(fullUrl, {
      ...options,
      headers,
      credentials: 'include',
    });
  } catch (error) {
    throw new ApiError('Terjadi kesalahan jaringan. Gagal terhubung ke server.', 0);
  }

  if (!response.ok) {
     throw new ApiError(`Request gagal dengan status ${response.status}`, response.status);
  }
  return response;
}
