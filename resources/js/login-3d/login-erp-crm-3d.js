import * as THREE from 'three';

const container = document.getElementById('login-erp-crm-3d');

if (container) {
    initSolutcloudLogin3D(container);
}

function initSolutcloudLogin3D(container) {
    const reducedMotion = window.matchMedia(
        '(prefers-reduced-motion: reduce)'
    ).matches;

    /* =========================================================
       SCÈNE
    ========================================================= */

    const scene = new THREE.Scene();

    const camera = new THREE.PerspectiveCamera(
        38,
        container.clientWidth / container.clientHeight,
        0.1,
        100
    );

    camera.position.set(0, 0.15, 11.5);

    const renderer = new THREE.WebGLRenderer({
        antialias: true,
        alpha: true,
        powerPreference: 'high-performance',
    });

    renderer.setPixelRatio(
        Math.min(window.devicePixelRatio || 1, 1.8)
    );

    renderer.setSize(
        container.clientWidth,
        container.clientHeight
    );

    renderer.setClearColor(0x000000, 0);

    renderer.outputColorSpace = THREE.SRGBColorSpace;

    container.appendChild(renderer.domElement);

    /* =========================================================
       GROUPES
    ========================================================= */

    const universe = new THREE.Group();
    scene.add(universe);

    const coreGroup = new THREE.Group();
    const erpGroup = new THREE.Group();
    const crmGroup = new THREE.Group();
    const connectionGroup = new THREE.Group();

    universe.add(
        connectionGroup,
        coreGroup,
        erpGroup,
        crmGroup
    );

    /* =========================================================
       LUMIÈRES
    ========================================================= */

    const ambient = new THREE.AmbientLight(
        0xffffff,
        1.35
    );

    scene.add(ambient);

    const tealLight = new THREE.PointLight(
        0x5bc6cd,
        13,
        15,
        2
    );

    tealLight.position.set(0, 1.4, 4);

    scene.add(tealLight);

    const leftLight = new THREE.PointLight(
        0x99e3e7,
        5,
        9
    );

    leftLight.position.set(-5, 1, 3);

    scene.add(leftLight);

    const rightLight = new THREE.PointLight(
        0xffffff,
        4,
        9
    );

    rightLight.position.set(5, 0.5, 3);

    scene.add(rightLight);

    /* =========================================================
       COULEURS
    ========================================================= */

    const COLORS = {
        teal: 0x2b909a,
        tealBright: 0x68d1d7,
        tealSoft: 0x7fd8dd,

        white: 0xffffff,

        erp: 0x61c3ca,
        crm: 0x9adfe3,

        line: 0x4aaab3,
        dark: 0x102f34,
    };

    /* =========================================================
       ÉTOILES / PARTICULES
    ========================================================= */

    const starsGeometry = new THREE.BufferGeometry();

    const starCount = 180;
    const starPositions = new Float32Array(
        starCount * 3
    );

    for (let i = 0; i < starCount; i++) {
        const i3 = i * 3;

        starPositions[i3] =
            (Math.random() - 0.5) * 15;

        starPositions[i3 + 1] =
            (Math.random() - 0.5) * 9;

        starPositions[i3 + 2] =
            -2 - Math.random() * 8;
    }

    starsGeometry.setAttribute(
        'position',
        new THREE.BufferAttribute(
            starPositions,
            3
        )
    );

    const starsMaterial =
        new THREE.PointsMaterial({
            color: COLORS.white,
            size: 0.025,
            transparent: true,
            opacity: 0.38,
            depthWrite: false,
        });

    const stars = new THREE.Points(
        starsGeometry,
        starsMaterial
    );

    universe.add(stars);

    /* =========================================================
       OUTILS TEXTE
    ========================================================= */

    function createTextSprite(
        text,
        {
            fontSize = 52,
            color = '#ffffff',
            weight = '600',
            opacity = 1,
            scale = 1,
        } = {}
    ) {
        const canvas =
            document.createElement('canvas');

        const context =
            canvas.getContext('2d');

        canvas.width = 1536;
        canvas.height = 384;

        context.clearRect(
            0,
            0,
            canvas.width,
            canvas.height
        );

        context.font =
            `${weight} ${fontSize}px Inter, Arial, sans-serif`;

        context.fillStyle = color;

        context.textAlign = 'center';
        context.textBaseline = 'middle';

        context.globalAlpha = opacity;

        context.fillText(
            text,
            canvas.width / 2,
            canvas.height / 2
        );

        const texture =
            new THREE.CanvasTexture(canvas);

        texture.colorSpace =
            THREE.SRGBColorSpace;

        texture.needsUpdate = true;

        const material =
            new THREE.SpriteMaterial({
                map: texture,
                transparent: true,
                depthWrite: false,
            });

        const sprite =
            new THREE.Sprite(material);

        sprite.scale.set(
            4.2 * scale,
            1.05 * scale,
            1
        );

        return sprite;
    }

    /* =========================================================
       NOYAU SOLUTCLOUD
    ========================================================= */

    const coreGeometry =
        new THREE.IcosahedronGeometry(
            1.05,
            4
        );

    const coreMaterial =
        new THREE.MeshPhysicalMaterial({
            color: COLORS.teal,
            roughness: 0.18,
            metalness: 0.12,

            transmission: 0.12,
            transparent: true,
            opacity: 0.94,

            emissive: COLORS.teal,
            emissiveIntensity: 0.17,

            clearcoat: 1,
            clearcoatRoughness: 0.18,
        });

    const core =
        new THREE.Mesh(
            coreGeometry,
            coreMaterial
        );

    coreGroup.add(core);

    const ringMaterial =
        new THREE.MeshBasicMaterial({
            color: COLORS.tealBright,
            transparent: true,
            opacity: 0.34,
            depthWrite: false,
        });

    const ring1 =
        new THREE.Mesh(
            new THREE.TorusGeometry(
                1.5,
                0.012,
                8,
                100
            ),
            ringMaterial
        );

    ring1.rotation.x =
        Math.PI / 2.6;

    ring1.rotation.z =
        Math.PI / 8;

    coreGroup.add(ring1);

    const ring2 =
        new THREE.Mesh(
            new THREE.TorusGeometry(
                1.72,
                0.007,
                8,
                100
            ),
            ringMaterial.clone()
        );

    ring2.material.opacity = 0.16;

    ring2.rotation.x =
        Math.PI / 2;

    ring2.rotation.y =
        Math.PI / 3;

    coreGroup.add(ring2);

    const coreTitle =
        createTextSprite(
            'SOLUTCLOUD',
            {
                fontSize: 100,
                weight: '800',
                scale: 1.05,
            }
        );

    coreTitle.position.set(
        0,
        -1.65,
        0.6
    );

    coreGroup.add(coreTitle);

    const coreSubtitle =
        createTextSprite(
            'ERP  +  CRM',
            {
                fontSize: 100,
                color: '#9edfe3',
                weight: '700',
                scale: 0.72,
            }
        );

    coreSubtitle.position.set(
        0,
        -2.03,
        0.55
    );

    coreGroup.add(coreSubtitle);

    /* =========================================================
       CRÉATION MODULE
    ========================================================= */

    const interactiveObjects = [];

    function createModule({
        title,
        position,
        group,
        type,
    }) {
        const nodeGroup =
            new THREE.Group();

        nodeGroup.position.copy(position);

        const geometry =
            new THREE.SphereGeometry(
                0.30,
                32,
                32
            );

        const material =
            new THREE.MeshPhysicalMaterial({
                color:
                    type === 'erp'
                        ? COLORS.erp
                        : COLORS.crm,

                roughness: 0.26,
                metalness: 0.06,

                emissive:
                    type === 'erp'
                        ? COLORS.erp
                        : COLORS.crm,

                emissiveIntensity:
                    0.07,

                transparent: true,
                opacity: 0.93,
            });

        const mesh =
            new THREE.Mesh(
                geometry,
                material
            );

        mesh.userData = {
            title,
            type,
            nodeGroup,
            originalScale: 1,
        };

        nodeGroup.add(mesh);

        const halo =
            new THREE.Mesh(
                new THREE.RingGeometry(
                    0.40,
                    0.47,
                    40
                ),
                new THREE.MeshBasicMaterial({
                    color:
                        type === 'erp'
                            ? COLORS.erp
                            : COLORS.crm,

                    transparent: true,
                    opacity: 0.13,

                    side: THREE.DoubleSide,
                    depthWrite: false,
                })
            );

        halo.userData.isHalo = true;

        nodeGroup.add(halo);

        const labelOffset =
            title.length > 8
                ? 0.98
                : title.length <= 3
                    ? 0.68
                    : 0.8;

        const label =
            createTextSprite(
                title,
                {
                    fontSize: 82,
                    color: '#ffffff',
                    weight: '700',
                    scale: 0.92,
                    opacity: 1,
                }
            );

        label.position.set(
            type === 'erp'
                ? -labelOffset
                : labelOffset,
            0,
            0.15
        );
        nodeGroup.add(label);

        interactiveObjects.push(mesh);

        group.add(nodeGroup);

        return {
            group: nodeGroup,
            mesh,
            halo,
            label,
        };
    }

    /* =========================================================
       ERP
    ========================================================= */

    const erpTitle =
        createTextSprite(
            'ERP',
            {
                fontSize: 250,
                color: '#74d0d6',
                weight: '800',
                scale: 0.98,
            }
        );

    erpTitle.position.set(
        -4.2,
        2.55,
        0
    );

    universe.add(erpTitle);

    const erpModules = [
        createModule({
            title: 'Finance',
            position:
                new THREE.Vector3(
                    -4.25,
                    1.25,
                    0
                ),
            group: erpGroup,
            type: 'erp',
        }),

        createModule({
            title: 'Stocks',
            position:
                new THREE.Vector3(
                    -4.65,
                    0.15,
                    0
                ),
            group: erpGroup,
            type: 'erp',
        }),

        createModule({
            title: 'Achats',
            position:
                new THREE.Vector3(
                    -4.25,
                    -1.0,
                    0
                ),
            group: erpGroup,
            type: 'erp',
        }),

        createModule({
            title: 'RH',
            position:
                new THREE.Vector3(
                    -3.45,
                    -2.0,
                    0
                ),
            group: erpGroup,
            type: 'erp',
        }),
    ];

    /* =========================================================
       CRM
    ========================================================= */

    const crmTitle =
        createTextSprite(
            'CRM',
            {
                fontSize: 250,
                color: '#b2e7ea',
                weight: '800',
                scale: 0.98,
            }
        );

    crmTitle.position.set(
        4.2,
        2.55,
        0
    );

    universe.add(crmTitle);

    const crmModules = [
        createModule({
            title: 'Prospects',
            position:
                new THREE.Vector3(
                    4.25,
                    1.25,
                    0
                ),
            group: crmGroup,
            type: 'crm',
        }),

        createModule({
            title: 'Clients',
            position:
                new THREE.Vector3(
                    4.65,
                    0.15,
                    0
                ),
            group: crmGroup,
            type: 'crm',
        }),

        createModule({
            title: 'Ventes',
            position:
                new THREE.Vector3(
                    4.25,
                    -1.0,
                    0
                ),
            group: crmGroup,
            type: 'crm',
        }),

        createModule({
            title: 'Suivi client',
            position:
                new THREE.Vector3(
                    3.45,
                    -2.0,
                    0
                ),
            group: crmGroup,
            type: 'crm',
        }),
    ];

    /* =========================================================
       CONNEXIONS
    ========================================================= */

    function createConnection(
        from,
        to,
        opacity = 0.25
    ) {
        const geometry =
            new THREE.BufferGeometry()
                .setFromPoints([
                    from,
                    to,
                ]);

        const material =
            new THREE.LineBasicMaterial({
                color: COLORS.line,
                transparent: true,
                opacity,
                depthWrite: false,
            });

        const line =
            new THREE.Line(
                geometry,
                material
            );

        connectionGroup.add(line);

        return line;
    }

    const center =
        new THREE.Vector3(
            0,
            0,
            0
        );

    erpModules.forEach(
        (module) => {
            createConnection(
                module.group.position,
                center
            );
        }
    );

    crmModules.forEach(
        (module) => {
            createConnection(
                module.group.position,
                center
            );
        }
    );

    /* =========================================================
       POINTEUR + RAYCASTER
    ========================================================= */

    const pointer =
        new THREE.Vector2(
            999,
            999
        );

    const pointerTarget = {
        x: 0,
        y: 0,
    };

    const currentPointer = {
        x: 0,
        y: 0,
    };

    const raycaster =
        new THREE.Raycaster();

    let hovered = null;

    function resetHovered() {
        if (!hovered) {
            return;
        }

        hovered.material.emissiveIntensity =
            0.07;

        hovered.userData.nodeGroup.scale.setScalar(
            1
        );

        hovered = null;

        container.style.cursor =
            'default';
    }

    function onPointerMove(event) {
        const rect =
            renderer.domElement
                .getBoundingClientRect();

        const x =
            (event.clientX - rect.left) /
            rect.width;

        const y =
            (event.clientY - rect.top) /
            rect.height;

        pointer.x =
            x * 2 - 1;

        pointer.y =
            -(y * 2 - 1);

        pointerTarget.x =
            pointer.x;

        pointerTarget.y =
            pointer.y;
    }

    function onPointerLeave() {
        pointer.set(
            999,
            999
        );

        pointerTarget.x = 0;
        pointerTarget.y = 0;

        resetHovered();
    }

    renderer.domElement.addEventListener(
        'pointermove',
        onPointerMove
    );

    renderer.domElement.addEventListener(
        'pointerleave',
        onPointerLeave
    );

    /* =========================================================
       INTERACTION
    ========================================================= */

    function updateHover() {
        raycaster.setFromCamera(
            pointer,
            camera
        );

        const hits =
            raycaster.intersectObjects(
                interactiveObjects,
                false
            );

        const nextHovered =
            hits.length
                ? hits[0].object
                : null;

        if (
            nextHovered === hovered
        ) {
            return;
        }

        resetHovered();

        if (nextHovered) {
            hovered =
                nextHovered;

            hovered.material
                .emissiveIntensity = 0.7;

            hovered.userData.nodeGroup
                .scale
                .setScalar(1.18);

            container.style.cursor =
                'pointer';
        }
    }

    /* =========================================================
       RESIZE
    ========================================================= */

    function resize() {
        const width =
            container.clientWidth;

        const height =
            container.clientHeight;

        if (!width || !height) {
            return;
        }

        camera.aspect =
            width / height;

        camera.updateProjectionMatrix();

        renderer.setSize(
            width,
            height,
            false
        );
    }

    const resizeObserver =
        new ResizeObserver(resize);

    resizeObserver.observe(container);

    resize();

    /* =========================================================
       ANIMATION
    ========================================================= */

    const clock =
        new THREE.Clock();

    let animationFrame = null;

    function animate() {
        animationFrame =
            requestAnimationFrame(
                animate
            );

        const elapsed =
            clock.getElapsedTime();

        currentPointer.x +=
            (
                pointerTarget.x -
                currentPointer.x
            ) * 0.035;

        currentPointer.y +=
            (
                pointerTarget.y -
                currentPointer.y
            ) * 0.035;

        if (!reducedMotion) {
            universe.rotation.y =
                currentPointer.x *
                0.055;

            universe.rotation.x =
                -currentPointer.y *
                0.025;

            core.rotation.y +=
                0.0022;

            core.rotation.x =
                Math.sin(
                    elapsed * 0.42
                ) * 0.06;

            ring1.rotation.z +=
                0.0018;

            ring2.rotation.y -=
                0.0013;

            coreGroup.position.y =
                Math.sin(
                    elapsed * 0.75
                ) * 0.075;

            stars.rotation.y +=
                0.0001;

            erpModules.forEach(
                (module, index) => {
                    module.group.position.z =
                        Math.sin(
                            elapsed * 0.75 +
                            index
                        ) * 0.06;
                }
            );

            crmModules.forEach(
                (module, index) => {
                    module.group.position.z =
                        Math.sin(
                            elapsed * 0.75 +
                            index +
                            1.5
                        ) * 0.06;
                }
            );
        }

        updateHover();

        renderer.render(
            scene,
            camera
        );
    }

    animate();

    /* =========================================================
       CLEANUP
    ========================================================= */

    window.addEventListener(
        'beforeunload',
        () => {
            if (animationFrame) {
                cancelAnimationFrame(
                    animationFrame
                );
            }

            resizeObserver.disconnect();

            renderer.dispose();

            renderer.domElement
                .removeEventListener(
                    'pointermove',
                    onPointerMove
                );

            renderer.domElement
                .removeEventListener(
                    'pointerleave',
                    onPointerLeave
                );
        },
        { once: true }
    );
}