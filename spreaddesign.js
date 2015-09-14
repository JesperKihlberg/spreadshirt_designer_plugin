
//function pushImage(obj) {
//    if (originalImage.src === undefined) {
//        originalImage.src = obj.src;
//        originalImage.width = obj.style.width;
//        originalImage.height = obj.style.height;
//    }
//}

//function popImage(obj) {
//    obj.src = originalImage.src;
//    obj.style.width = originalImage.width;
//    obj.style.height = originalImage.height;
//}

//function mouseOverImage2() {
//    var catPic = getCatPic();
//    pushImage(catPic);

//    // Hack away at image here ...
//    catPic.src = 'http://dummyimage.com/280x500/000/aaa';
//    catPic.style.width = '280px';
//    catPic.style.height = '500px';
//}

//function mouseOverImage3() {
//    var catPic = getCatPic();
//    pushImage(catPic);

//    // Hack away at image here ...
//    catPic.src = 'http://dummyimage.com/150x266/000/fff';
//    catPic.style.width = '150px';
//    catPic.style.height = '266px';
//}

//function mouseOutImage() {
//    var catPic = getCatPic();
//    // ... Restore Image here
//    popImage(catPic);
//}