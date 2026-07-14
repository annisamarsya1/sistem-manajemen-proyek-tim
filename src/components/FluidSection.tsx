import React, { useEffect, useRef } from 'react';
import * as THREE from 'three';
import { vertexShader, fluidFragmentShader, displayFragmentShader } from './fluidShaders';
import heroImageFront from '../assets/front.webp';
import heroImageBack from '../assets/back.jpg';

/**
 * Komponen FluidSection
 * 
 * Menggunakan Three.js dan WebGL shader kustom untuk merender efek cairan interaktif
 * yang bereaksi terhadap pergerakan kursor mouse pengguna.
 * Sangat intensif secara performa, dioptimalkan dengan IntersectionObserver agar
 * hanya merender saat terlihat di layar.
 */
export default function FluidSection() {
  const containerRef = useRef<HTMLElement>(null);

  useEffect(() => {
    const container = containerRef.current;
    if (!container) return;
    
    let renderer: THREE.WebGLRenderer;
    let camera: THREE.OrthographicCamera;
    let simScene: THREE.Scene;
    let scene: THREE.Scene;
    let pingPongTargets: THREE.WebGLRenderTarget[];
    let currentTarget = 0;
    let trailsMaterial: THREE.ShaderMaterial;
    let displayMaterial: THREE.ShaderMaterial;
    let geometry: THREE.PlaneGeometry;

    let isMoving = false; // Melacak apakah mouse sedang bergerak
    let lastMoveTime = 0; // Waktu terakhir pergerakan mouse (untuk menghentikan simulasi jika diam)
    const mouse = new THREE.Vector2(); // Posisi mouse saat ini
    const prevMouse = new THREE.Vector2(); // Posisi mouse pada frame sebelumnya
    let animationFrameId: number;

    const size = 500; // Resolusi tekstur simulasi

    // Inisialisasi WebGL Renderer
    renderer = new THREE.WebGLRenderer({ antialias: false, alpha: true });
    // Membatasi pixel ratio maksimum untuk alasan performa
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 1.25));
    renderer.setSize(container.clientWidth, container.clientHeight);
    
    // Inject canvas ke dalam container komponen
    const canvas = renderer.domElement;
    canvas.className = 'fluid-reveal';
    canvas.style.position = 'absolute';
    canvas.style.top = '0';
    canvas.style.left = '0';
    canvas.style.width = '100%';
    canvas.style.height = '100%';
    canvas.style.pointerEvents = 'none';
    canvas.style.zIndex = '0';
    
    container.insertBefore(canvas, container.firstChild);

    // Ping-pong targets digunakan untuk mensimulasikan state buffer bolak-balik
    // sehingga output dari frame sebelumnya bisa menjadi input frame berikutnya (untuk fluid flow)
    pingPongTargets = [
      new THREE.WebGLRenderTarget(size, size, { minFilter: THREE.LinearFilter, magFilter: THREE.LinearFilter, format: THREE.RGBAFormat, type: THREE.FloatType }),
      new THREE.WebGLRenderTarget(size, size, { minFilter: THREE.LinearFilter, magFilter: THREE.LinearFilter, format: THREE.RGBAFormat, type: THREE.FloatType }),
    ];
    
    // Bersihkan buffer dari garbage memory
    renderer.setRenderTarget(pingPongTargets[0]);
    renderer.clear();
    renderer.setRenderTarget(pingPongTargets[1]);
    renderer.clear();
    renderer.setRenderTarget(null);

    // Menggunakan OrthographicCamera karena kita hanya menggambar plane 2D layar penuh
    camera = new THREE.OrthographicCamera(-1, 1, 1, -1, 0, 1);
    geometry = new THREE.PlaneGeometry(2, 2);

    // Setup scene untuk simulasi physics/trail cairan
    simScene = new THREE.Scene();
    trailsMaterial = new THREE.ShaderMaterial({
      uniforms: {
        uPrevTrails: { value: null },
        uMouse: { value: mouse },
        uPrevMouse: { value: prevMouse },
        uResolution: { value: new THREE.Vector2(size, size) },
        uDecay: { value: 0.975 },
        uLineWidth: { value: 0.22 },
        uIntensity: { value: 0.5 },
        uIsMoving: { value: false },
      },
      vertexShader,
      fragmentShader: fluidFragmentShader,
    });
    simScene.add(new THREE.Mesh(geometry, trailsMaterial));

    scene = new THREE.Scene();

    const textureLoader = new THREE.TextureLoader();
    let textureSize = new THREE.Vector2(1, 1);
    
    // Load top texture
    const textureTop = textureLoader.load(heroImageFront, (tex) => {
      textureSize.set(tex.image.width, tex.image.height);
      displayMaterial.uniforms.uTopTextureSize.value.copy(textureSize);
      displayMaterial.uniforms.uBottomTextureSize.value.copy(textureSize); // assuming both are same size
    });
    
    // Load bottom texture
    const textureBottom = textureLoader.load(heroImageBack);

    displayMaterial = new THREE.ShaderMaterial({
      uniforms: {
        uFluid: { value: null },
        uTopTexture: { value: textureTop },
        uBottomTexture: { value: textureBottom },
        uResolution: { value: new THREE.Vector2(container.clientWidth, container.clientHeight) },
        uDpr: { value: renderer.getPixelRatio() },
        uTopTextureSize: { value: textureSize },
        uBottomTextureSize: { value: textureSize },
        uTime: { value: 0 },
        uEdgeNoiseScale: { value: 14.0 },
        uEdgeNoiseStrength: { value: 0.3 },
        uWarpAmount: { value: 0.06 },
        uThreshold: { value: 0.02 },
      },
      vertexShader,
      fragmentShader: displayFragmentShader,
    });
    scene.add(new THREE.Mesh(geometry, displayMaterial));

    const handlePointerMove = (e: PointerEvent) => {
      const rect = container.getBoundingClientRect();
      const x = (e.clientX - rect.left) / rect.width;
      const y = 1.0 - ((e.clientY - rect.top) / rect.height);
      
      if (!isMoving) {
        prevMouse.set(x, y);
        mouse.set(x, y);
      } else {
        prevMouse.copy(mouse);
        mouse.set(x, y);
      }
      
      isMoving = true;
      lastMoveTime = performance.now();
    };
    
    container.addEventListener('pointermove', handlePointerMove as any);

    // Handle resize untuk memperbarui ukuran renderer dan rasio material
    const handleResize = () => {
      const w = container.clientWidth;
      const h = container.clientHeight;
      renderer.setSize(w, h);
      displayMaterial.uniforms.uResolution.value.set(w, h);
    };
    window.addEventListener('resize', handleResize);

    // Menggunakan IntersectionObserver agar WebGL hanya berjalan jika elemen terlihat
    // Ini menghemat pemakaian GPU sangat besar saat elemen di-scroll ke luar layar
    let isVisible = false;
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        isVisible = entry.isIntersecting;
      });
    }, { threshold: 0 });
    observer.observe(container);

    const animate = () => {
      animationFrameId = requestAnimationFrame(animate);
      
      if (!isVisible) return; // Skip heavy rendering if not visible

      if (isMoving && performance.now() - lastMoveTime > 50) isMoving = false;
      
      displayMaterial.uniforms.uTime.value = performance.now() * 0.001;
      
      const prevTarget = pingPongTargets[currentTarget];
      currentTarget = (currentTarget + 1) % 2;
      const currentRenderTarget = pingPongTargets[currentTarget];
      
      trailsMaterial.uniforms.uPrevTrails.value = prevTarget.texture;
      trailsMaterial.uniforms.uMouse.value.copy(mouse);
      trailsMaterial.uniforms.uPrevMouse.value.copy(prevMouse);
      trailsMaterial.uniforms.uIsMoving.value = isMoving;
      
      renderer.setRenderTarget(currentRenderTarget);
      renderer.render(simScene, camera);
      
      displayMaterial.uniforms.uFluid.value = currentRenderTarget.texture;
      renderer.setRenderTarget(null);
      renderer.render(scene, camera);
    };
    
    animate();

    return () => {
      cancelAnimationFrame(animationFrameId);
      container.removeEventListener('pointermove', handlePointerMove as any);
      window.removeEventListener('resize', handleResize);
      observer.disconnect();
      
      geometry.dispose();
      trailsMaterial.dispose();
      displayMaterial.dispose();
      textureTop.dispose();
      textureBottom.dispose();
      pingPongTargets[0].dispose();
      pingPongTargets[1].dispose();
      renderer.dispose();
      
      if (canvas.parentNode) {
        canvas.parentNode.removeChild(canvas);
      }
    };
  }, []);

  return (
    <section 
      id="about"
      ref={containerRef} 
      className="relative w-full h-[800px] lg:h-[900px] overflow-hidden bg-[#060b13] border-t border-blue-900/30 flex items-center justify-center"
    >
      <div className="relative z-10 text-center pointer-events-none px-6 py-10 md:px-12 md:py-12 bg-[#060b13]/75 backdrop-blur-md rounded-3xl border border-white/10 shadow-2xl max-w-3xl mx-4">
        <h2 className="text-3xl md:text-5xl font-extrabold text-white mb-6 tracking-tight drop-shadow-lg">About Us</h2>
        <p className="text-slate-200 text-sm md:text-lg leading-relaxed drop-shadow-md">
          TaskSync adalah platform manajemen tugas komprehensif yang dirancang untuk menyatukan tim Anda. Kami mengintegrasikan alur kerja visual (Kanban), pelacakan jam kerja yang presisi, serta dasbor analitik mendalam—memberdayakan Anda untuk memantau produktivitas, berkolaborasi secara efisien, dan menuntaskan proyek dengan tepat waktu.
        </p>
      </div>
    </section>
  );
}
