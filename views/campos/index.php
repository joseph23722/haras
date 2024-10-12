<?php require_once '../../header.php'; ?>

<h1>Visualizador de Archivo CAD (DXF)</h1>

<!-- Div donde se renderizará el archivo -->
<div id="viewer" style="width: 100%; height: 80vh;"></div>

<!-- Importar Three.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<!-- Importar OrbitControls de Three.js -->
<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
<!-- Importar el archivo generado three-dxf.js -->
<script src="/haras/three-dxf/dist/three-dxf.js" onload="console.log('three-dxf.js cargado correctamente')" onerror="console.error('Error al cargar three-dxf.js')"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Verifica si DxfParser está disponible
    console.log(window.DxfParser);  // Verifica si DxfParser está cargado correctamente

    if (typeof window.DxfParser !== 'function') {
        console.error('DxfParser no está disponible correctamente.');
        return;
    }

    function loadDXF(filePath) {
        // Crear la escena, cámara y renderizador
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
        const renderer = new THREE.WebGLRenderer();
        renderer.setSize(window.innerWidth, window.innerHeight);
        document.getElementById('viewer').appendChild(renderer.domElement);

        // Configurar controles de órbita para mover la cámara
        const controls = new THREE.OrbitControls(camera, renderer.domElement);
        controls.enableZoom = true;
        controls.enableRotate = true;
        controls.enablePan = true;

        // Cargar el archivo DXF
        const loader = new THREE.FileLoader();
        loader.load(filePath, function (data) {
            try {
                const parser = new window.DxfParser();  // Verifica si DxfParser se inicializa correctamente
                const dxf = parser.parseSync(data);  // Procesar el archivo DXF
                const group = new THREE.Group();

                // Variables para calcular los límites del dibujo
                let minX = Infinity, minY = Infinity, maxX = -Infinity, maxY = -Infinity;

                // Recorrer las entidades y agregar líneas a la escena
                dxf.entities.forEach(entity => {
                    if (entity.type === 'LINE') {
                        const geometry = new THREE.BufferGeometry().setFromPoints([
                            new THREE.Vector3(entity.vertices[0].x, entity.vertices[0].y, 0),
                            new THREE.Vector3(entity.vertices[1].x, entity.vertices[1].y, 0)
                        ]);
                        const material = new THREE.LineBasicMaterial({ color: 0x0000ff });
                        const line = new THREE.Line(geometry, material);
                        group.add(line);

                        // Actualizar los límites
                        minX = Math.min(minX, entity.vertices[0].x, entity.vertices[1].x);
                        minY = Math.min(minY, entity.vertices[0].y, entity.vertices[1].y);
                        maxX = Math.max(maxX, entity.vertices[0].x, entity.vertices[1].x);
                        maxY = Math.max(maxY, entity.vertices[0].y, entity.vertices[1].y);
                    }
                });

                scene.add(group);

                // Centrar la cámara y ajustar el zoom
                const centerX = (minX + maxX) / 2;
                const centerY = (minY + maxY) / 2;
                const sizeX = maxX - minX;
                const sizeY = maxY - minY;
                const maxSize = Math.max(sizeX, sizeY);

                camera.position.set(centerX, centerY, maxSize * 1.5);
                camera.lookAt(centerX, centerY, 0);
                camera.updateProjectionMatrix();

                // Función de animación para renderizar la escena
                function animate() {
                    requestAnimationFrame(animate);
                    controls.update();
                    renderer.render(scene, camera);
                }

                animate();
            } catch (error) {
                console.error('Error al procesar el archivo DXF:', error);
            }
        });

        // Ajustar el tamaño del renderizador al cambiar el tamaño de la ventana
        window.addEventListener('resize', function () {
            const width = window.innerWidth;
            const height = window.innerHeight;
            renderer.setSize(width, height);
            camera.aspect = width / height;
            camera.updateProjectionMatrix();
        });
    }

    // Cargar el archivo DXF específico
    loadDXF('/haras/views/campos/plano2024.dxf');
});
</script>

<?php require_once '../../footer.php'; ?>