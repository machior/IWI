
function createSphere(radius, meridian, circles)
{
    geometry = new THREE.SphereGeometry( radius, meridian, circles );
//    material = new THREE.MeshBasicMaterial( { color: 0x888888, wireframe: true } );
    material = new THREE.MeshBasicMaterial( { color: 0x007700, wireframe: true } );
    return new THREE.Mesh(geometry, material);
}

function render() 
{
    renderer.render(scene, camera);
    requestAnimFrame(render);
}