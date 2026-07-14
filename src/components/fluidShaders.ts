/**
 * Shader GLSL untuk efek cairan (Fluid) di Landing Page.
 * Berisi Vertex Shader standar dan Fragment Shader kustom untuk 
 * menghasilkan trail mouse dan efek distorsi gambar.
 */

// Vertex Shader Dasar: Meneruskan UV dan menghitung posisi proyeksi 2D
export const vertexShader = `
  varying vec2 vUv;
  void main() {
    vUv = uv;
    gl_Position = projectionMatrix * modelViewMatrix * vec4(position, 1.0);
  }
`;

// Fragment Shader untuk Simulasi Cairan (Trails)
// Menghitung trail (jejak) berdasarkan pergerakan kursor mouse dan mengurangi intensitas seiring waktu (decay)
export const fluidFragmentShader = `
  uniform sampler2D uPrevTrails;
  uniform vec2 uMouse;
  uniform vec2 uPrevMouse;
  uniform vec2 uResolution;
  uniform float uDecay;
  uniform float uLineWidth;
  uniform float uIntensity;
  uniform bool uIsMoving;
  varying vec2 vUv;
  void main() {
    vec4 prevState = texture2D(uPrevTrails, vUv);
    float newValue = prevState.r * uDecay;
    if (uIsMoving) {
      vec2 mouseDirection = uMouse - uPrevMouse;
      float lineLength = length(mouseDirection);
      if (lineLength > 0.001) {
        vec2 mouseDir = mouseDirection / lineLength;
        vec2 toPixel = vUv - uPrevMouse;
        float projAlong = dot(toPixel, mouseDir);
        projAlong = clamp(projAlong, 0.0, lineLength);
        vec2 closestPoint = uPrevMouse + projAlong * mouseDir;
        float dist = length(vUv - closestPoint);
        float intensity = smoothstep(uLineWidth, 0.0, dist) * uIntensity;
        newValue += intensity;
      }
    }
    gl_FragColor = vec4(newValue, 0.0, 0.0, 1.0);
  }
`;

// Fragment Shader untuk Display/Render Akhir
// Mengambil jejak cairan (uFluid) dari shader sebelumnya dan mendistorsi transisi 
// antara dua gambar (uTopTexture dan uBottomTexture)
export const displayFragmentShader = `
  uniform sampler2D uFluid;
  uniform sampler2D uTopTexture;
  uniform sampler2D uBottomTexture;
  uniform vec2 uResolution;
  uniform float uDpr;
  uniform vec2 uTopTextureSize;
  uniform vec2 uBottomTextureSize;
  
  uniform float uTime;
  uniform float uEdgeNoiseScale;
  uniform float uEdgeNoiseStrength;
  uniform float uWarpAmount;
  uniform float uThreshold;
  
  varying vec2 vUv;
  
  float hash(vec2 p){ return fract(sin(dot(p, vec2(127.1, 311.7))) * 43758.5453123); }
  float noise(vec2 p){
    vec2 i = floor(p), f = fract(p);
    float a = hash(i), b = hash(i + vec2(1.0, 0.0));
    float c = hash(i + vec2(0.0, 1.0)), d = hash(i + vec2(1.0, 1.0));
    vec2 u = f * f * (3.0 - 2.0 * f);
    return mix(mix(a, b, u.x), mix(c, d, u.x), u.y);
  }
  float fbm(vec2 p){
    float v = 0.0, a = 0.5;
    for (int i = 0; i < 4; i++){ v += a * noise(p); p *= 2.0; a *= 0.5; }
    return v;
  }

  vec2 getCoverUV(vec2 uv, vec2 textureSize) {
    if (textureSize.x < 1.0 || textureSize.y < 1.0) return uv;
    vec2 s = uResolution / textureSize;
    float scale = max(s.x, s.y);
    vec2 scaledSize = textureSize * scale;
    vec2 offset = (uResolution - scaledSize) * 0.5;
    return (uv * uResolution - offset) / scaledSize;
  }
  
  void main() {
    vec2 topUV = getCoverUV(vUv, uTopTextureSize);
    vec2 bottomUV = getCoverUV(vUv, uBottomTextureSize);
    vec4 topColor = texture2D(uTopTexture, topUV);
    vec4 bottomColor = texture2D(uBottomTexture, bottomUV);
    
    // Use original image with 80% opacity
    bottomColor = vec4(bottomColor.rgb, bottomColor.a * 0.8);
    
    vec2 warp = vec2(fbm(vUv * 4.0 + uTime * 0.03), fbm(vUv * 4.0 + 10.0)) - 0.5;
    float fluid = texture2D(uFluid, vUv + warp * uWarpAmount).r;

    float n = fbm(vUv * uEdgeNoiseScale + uTime * 0.05);
    // FIX A (recommended) — multiplicative gate: fluid=0 stays 0
    float distorted = fluid * (1.0 + (n - 0.5) * uEdgeNoiseStrength * 2.0);

    // hard, ragged boundary (tiny smoothstep just to avoid aliasing)
    float t = smoothstep(uThreshold, uThreshold + 0.006, distorted);
    vec4 finalColor = mix(topColor, bottomColor, t);
    gl_FragColor = finalColor;
  }
`;
