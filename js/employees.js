
//TUTAJ JEST SFERA

var scene, camera, renderer, pivot;
var geometry, material, innerSphere, outerSphere;
var projector, objects=[], innerObjects=[], outerObjects=[];
var innerRadius = $('#map').height()/4;
var outerRadius = $('#map').height()/2.2;
var snapRoad = outerRadius/innerRadius / 20;
var map = document.getElementById("map");
var isDragging, clickedItem, focusedItemPosition, selectedItemID, interval, radialInterval;
var itemsOnSpheresSwapped = false;
var connectedOpacity=1, disconnectedOpacity=0.4;
var photoZone, previousMousePosition;

init();

function init()
{
    ///////////////
    //  inicjalizacja sceny
    ///////////////
    scene = new THREE.Scene();
    camera = new THREE.PerspectiveCamera(30, window.innerWidth/window.innerHeight, 1, 5000);
    camera.position.z = 1000;
    
    // do pivota będą dołączane kolejne elementy przedstawianej mapy
    pivot = new THREE.Object3D();
    scene.add( pivot );
    
    //  stworzenie sfer
    innerSphere = createSphere(innerRadius, 9, 9);
    pivot.add(innerSphere);
    outerSphere = createSphere(outerRadius, 11, 11);
    pivot.add(outerSphere);

    ////////////////////////////////    USTAWIANIE ZDJĘĆ PRACOWNIKÓW NA SFERZE
    photoZone = document.getElementById('photoZone');
    for(i=0; i<(photoZone.children.length); ++i)
    {
        var obj = photoZone.children[i];
        var spr = new THREE.TextureLoader().load( obj.src );
        var material = new THREE.SpriteMaterial( { map: spr, opacity: disconnectedOpacity } );

        var sprite = new THREE.Sprite( material );
        setPositionOnSphere(sprite, outerRadius);

        pivot.add( sprite );
        objects.push( sprite );
        outerObjects.push( sprite );
        sprite.name = obj.id;
    }
    
    ////////////////////////////////    USTAWIANIE ZDJĘĆ STANOWISK NA SFERZE
    for(i=0; i<columns.length; ++i)
    {
        var string = "img/flags/" + columns[i] + ".png";
        var spr = new THREE.TextureLoader().load( string );
    //    var material = new THREE.SpriteMaterial( { map: spr, color: 0xffffff, fog: true } );
        var material = new THREE.SpriteMaterial( { map: spr, color: 0xffffff, fog: true, opacity: disconnectedOpacity } );

        var sprite = new THREE.Sprite( material );
        setPositionOnSphere(sprite, innerRadius);

        pivot.add( sprite );
        objects.push( sprite );
        innerObjects.push( sprite );
        sprite.name = columns[i];
    }

    /////////////////////////////////   STOWRZENIE OBIEKTU CANVAS I WSTAIWENIE GO DO "MAP"
    renderer = new THREE.WebGLRenderer();
    renderer.setSize( $('#map').width(), $('#map').height() );
    document.getElementById("map").appendChild(renderer.domElement);
    renderer.render( scene, camera );
    
    /////////////////////////////////   OBSŁUGA MYSZKI
    $(renderer.domElement).mousedown(mouseDownService);
    $(renderer.domElement).mouseup(mouseUpService);
    isDragging = false;
    $(renderer.domElement).mousemove(mouseMoveFunction);
    
    if (renderer.domElement.addEventListener) {
        renderer.domElement.addEventListener("mousewheel", MouseWheelHandler, false); // IE9, Chrome, Safari, Opera
        renderer.domElement.addEventListener("DOMMouseScroll", MouseWheelHandler, false); // Firefox
    }
    else renderer.domElement.attachEvent("onmousewheel", MouseWheelHandler); // IE 6/7/8
    
    previousMousePosition = {   x: 0, y: 0  };

    projector = new THREE.Projector();

    //////////////////////////////////  USTAWIENIE ANIMACJI
    window.requestAnimFrame = (function(){
        return  window.requestAnimationFrame ||
        window.webkitRequestAnimationFrame ||
        window.mozRequestAnimationFrame ||
            function(callback) {
                window.setTimeout(callback, 1000 / 50);
            };
    })();

    //////////////////////////////////  ZRENDEROWANIE SCENY
    render();
}



function setPositionOnSphere(sphereObject, radius){
    var theta = Math.random()*2*Math.PI;
    var phi = Math.acos(Math.random()*2-1);
    var x0 = radius*Math.sin(phi)*Math.cos(theta);
    var y0 = radius*Math.sin(phi)*Math.sin(theta);
    var z0 = radius*Math.cos(phi);
    
    sphereObject.scale.set(40, 40, 1);
    sphereObject.position.set(x0, y0, z0);
}

function mouseDownService(event){
//function onDocumentMouseDown( event ) {
    isDragging = true;
    focusedItemPosition = null;
    clearInterval(interval);
    
    var intersects = intersectsCalculation( event );
    
    // Check if there is any object
    if ( intersects.length > 0 ) {
        
        if(clickedItem == intersects[ 0 ].object.name)
        {
            clickedItem = null;
            clearInterval(rotationInterval);
            rotationEnabled = false;
            focusedItemPosition = intersects[ 0 ].object.matrixWorld;
            interval = setInterval(rotateSphereToFocus, 50);
        }
        
        else 
        {
            clickedItem = intersects[ 0 ].object.name;
            setTimeout(deleteClickedItem, 500);
        }
    }
}

function mouseUpService(event)
{
    isDragging = false;    
    var intersects = intersectsCalculation( event );
    
//    Check if there is any object
    if ( intersects.length > 0 )
        if(clickedItem == intersects[ 0 ].object.name)
            $('[id="' + clickedItem + '"]').click();
}

var justClickedPanelButton;
function leftPanelButtonClick(a)
{
    if(justClickedPanelButton != a)
    {
        var endPoints=[];
        var j=0;
        uncheckAll();
        checkItem(a.id);

        for(i=0; i<columns.length; ++i)
            if(table[a.id][columns[i]] == 1){
                checkItem(columns[i]);
                endPoints[j++] = columns[i];
                document.getElementById(columns[i]+"CheckBox").checked = true;
                document.getElementById("oldCountry").innerHTML = columns[i];
            }
        drawConnections(a.id, endPoints);

        var editTextField = document.getElementById("editTextField");
        editTextField.value = a.innerHTML;
        var newEditTextField = document.getElementById("newEditTextField");
        newEditTextField.value = a.innerHTML;
        var editTextLabel = document.getElementById("editTextLabel");
        editTextLabel.innerHTML = a.innerHTML;
        var editImage = document.getElementById("editImage");
        
        var image = photoZone.getElementsByTagName('img');
        for(i=0; i<image.length; ++i)
            if(image[i].id == a.id){
                editImage.src = image[i].src;
                break;
            }
//        editImage.src = "img/pg.jpg";
        document.getElementById("submitEdition").disabled = false;
        
        
        justClickedPanelButton = a;
        setTimeout(deleteJustClickedPanelButton, 500);
    }
    else
        doubleClickService(a.id);
}

function deleteJustClickedPanelButton() { justClickedPanelButton=null; }

function doubleClickService(obID)
{
    clearInterval(rotationInterval);
    rotationEnabled = false;
    justClickedPanelButton=null;
    
    for(i=0; i<objects.length; ++i)
        if(objects[i].name == obID){
            focusedItemPosition = objects[i].matrixWorld;
            break;
        }
    interval = setInterval(rotateSphereToFocus, 50);

    clickedItem = obID;
    setTimeout(deleteClickedItem, 500);

}

function rightPanelButtonClick(a)
{
    if(justClickedPanelButton != a)
    {
        var endPoints=[];
        var j=0;
        uncheckAll();
        checkItem(a.id);

        for(i=0; i<names.length; ++i)
            if(table[names[i]][a.id] == 1){
                checkItem(names[i]);
                endPoints[j++] = names[i];
            }
        drawConnections(a.id, endPoints);
        
        justClickedPanelButton = a;
        setTimeout(deleteJustClickedPanelButton, 500);
    }
    else
        doubleClickService(a.id);
}


function deleteClickedItem(){ clickedItem=null; }


var horMax = 10, verMax = 10;
var vector = new THREE.Vector3();
var vSign=0, hSign=0;
var deltaRotationQuaternion;

function rotateSphereToFocus()
{
    vector.setFromMatrixPosition( focusedItemPosition );
    
    hSign = 0;
    vSign = 0;
    
    if(Math.abs(vector.y) > horMax){
        if(vector.y>0)  vSign=1;
        else vSign=-1;
    }
    if(Math.abs(vector.x) > verMax){
        if(vector.x>0)  hSign=1;
        else hSign=-1;
    }
    
    if( hSign==0 && vSign==0 )
        clearInterval(interval);

    deltaRotationQuaternion = new THREE.Quaternion()
            .setFromEuler(new THREE.Euler(
                toRadians(vSign*3),
                toRadians(-hSign*3),
                0,
                'XYZ'
            ));

    pivot.quaternion.multiplyQuaternions(deltaRotationQuaternion, pivot.quaternion);
}

function mouseMoveFunction(e)
{
    var deltaMove = {
        x: e.offsetX-previousMousePosition.x,
        y: e.offsetY-previousMousePosition.y
    };

    if(isDragging) {

        var deltaRotationQuaternion = new THREE.Quaternion()
            .setFromEuler(new THREE.Euler(
                toRadians(deltaMove.y * 1),
                toRadians(deltaMove.x * 1),
                0,
                'XYZ'
            ));

        pivot.quaternion.multiplyQuaternions(deltaRotationQuaternion, pivot.quaternion);
    }

    previousMousePosition = {
        x: e.offsetX,
        y: e.offsetY
    };
}

function MouseWheelHandler(e) {

    // cross-browser wheel delta
    var e = window.event || e; // old IE support
    var delta = Math.max(-1, Math.min(1, (e.wheelDelta || -e.detail)));

    e.preventDefault();
    camera.position.z += delta*50;

    return false;
}

function toRadians(angle) { return angle * (Math.PI / 180); }

function toDegrees(angle) { return angle * (180 / Math.PI); }



function intersectsCalculation( event ){
    var mouse3D = new THREE.Vector3( ( (event.clientX - $(renderer.domElement).offset().left + window.pageXOffset) / $(renderer.domElement).width() ) * 2 - 1,   //x
                                        -( (event.clientY - $(renderer.domElement).offset().top + window.pageYOffset) / $(renderer.domElement).height() ) * 2 + 1,  //y
                                        0.5 );
    projector.unprojectVector( mouse3D, camera );   
    mouse3D.sub( camera.position );                
    mouse3D.normalize();
    var raycaster = new THREE.Raycaster( camera.position, mouse3D );
    return raycaster.intersectObjects( objects );
}


function drawConnections(startPoint, endPoints)
{
    selectedItemID = startPoint;
    var startPointPosition;
    var endPointPosition;
    
    while(pivot.getChildByName("line"))
        pivot.remove(pivot.getChildByName("line"));
    
    
    for(i=0; i<objects.length; ++i)
    {
        if(objects[i].name == startPoint){
            startPointPosition = objects[i].position;
            objects[i].material.opacity = connectedOpacity;
            objects[i].scale.set(70, 70, 1);
        }
        else{
            objects[i].material.opacity = disconnectedOpacity;
            objects[i].scale.set(40, 40, 1);
        }
    }
    
    for(j=0; j<endPoints.length; ++j)
    {
        for(i=0; i<objects.length; ++i)
            if(objects[i].name == endPoints[j]){
                endPointPosition = objects[i].position;
                objects[i].material.opacity = connectedOpacity;
                objects[i].scale.set(70, 70, 1);
                break;
            }
        var lineGeometry = new THREE.Geometry();
        lineGeometry.vertices.push( startPointPosition, endPointPosition );
        lineGeometry.computeLineDistances();
        var lineMaterial = new THREE.LineBasicMaterial( { color: 0xCC0000 } );
        var line = new THREE.Line( lineGeometry, lineMaterial );
        line.name="line";
        pivot.add(line);
    }
}

function checkItem(objId) {
    el = document.getElementById(objId);
    el.classList.remove('listItem');
    el.classList.add('selectedListItem');
}
function uncheckItem(el) {
    el.classList.remove('selectedListItem');
    el.classList.add('listItem');
}

function uncheckAll() 
{
    var el;
    for(i=0; i<names.length; ++i)
    {
        el = document.getElementById(names[i]);
        uncheckItem(el);
    }
    for(i=0; i<columns.length; ++i)
    {
        el = document.getElementById(columns[i]);
        uncheckItem(el);
        el = document.getElementById(columns[i]+"CheckBox");
        el.checked = false;
    }
    
    document.getElementById("oldCountry").value = "";
    document.getElementById("editTextField").value = "";
    document.getElementById("newEditTextField").value = "";
    document.getElementById("editTextLabel").innerHTML = "";
    document.getElementById("submitEdition").disabled = true;
}

function editButtonClicked(objId)
{
    console.log("objId id = " + objId.id);
    el = document.getElementById("editingTable");
    el.classList.toggle('editingFieldHidden');
    el.classList.toggle('editingFieldShown');
}

function swapItemsOnSpheres()
{
    if( !itemsOnSpheresSwapped )
    {
        itemsOnSpheresSwapped = true;
        
        for(i=0; i<innerObjects.length; ++i)
        {
//            innerBegin[i]
            
            innerObjects[i].position.x *= outerRadius/innerRadius;
            innerObjects[i].position.y *= outerRadius/innerRadius;
            innerObjects[i].position.z *= outerRadius/innerRadius;
            
        }
        for(i=0; i<outerObjects.length; ++i)
        {
            outerObjects[i].position.x *= innerRadius/outerRadius;
            outerObjects[i].position.y *= innerRadius/outerRadius;
            outerObjects[i].position.z *= innerRadius/outerRadius;
        }
        
    }
    else
    {
        for(i=0; i<innerObjects.length; ++i)
        {
            innerObjects[i].position.x /= outerRadius/innerRadius;
            innerObjects[i].position.y /= outerRadius/innerRadius;
            innerObjects[i].position.z /= outerRadius/innerRadius;
        }
        for(i=0; i<outerObjects.length; ++i)
        {
            outerObjects[i].position.x /= innerRadius/outerRadius;
            outerObjects[i].position.y /= innerRadius/outerRadius;
            outerObjects[i].position.z /= innerRadius/outerRadius;
        }
        itemsOnSpheresSwapped = false;
    }
    
    $('[id="' + selectedItemID + '"]').click();
}

function outerSphereVisible(a)
{
    if(outerSphere.visible){
        a.innerHTML = "OFF";
        outerSphere.visible = false;
    }
    else{
        a.innerHTML = "ON";
        outerSphere.visible = true;
    }
}

function innerSphereVisible(a)
{
    if(innerSphere.visible){
        a.innerHTML = "OFF";
        innerSphere.visible = false;
    }
    else{
        a.innerHTML = "ON";
        innerSphere.visible = true;
    }
}

var rotationEnabled = false;
var rotationInterval;
function automaticRotate(button){
    if(rotationEnabled){
        rotationEnabled = false;
        clearInterval(rotationInterval);
        button.innerHTML = "OFF";
    }
    else{
        rotationEnabled = true;
        rotationInterval = setInterval(pivotRotation, 50);
        button.innerHTML = "ON";
    }
}


var automaticQuaternion = new THREE.Quaternion()
        .setFromEuler(new THREE.Euler(
            toRadians(0.6),
            toRadians(0.5),
            0,
            'XYZ'
        ));

function pivotRotation(){
    pivot.quaternion.multiplyQuaternions(automaticQuaternion, pivot.quaternion);
}

function deleteEmployee()
{
    var form = document.getElementById("editingTable");
    form.setAttribute("action", "delete.php");
    form.submit();
}