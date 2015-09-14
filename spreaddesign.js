
function getArticleThumbs() {
    return document.getElementsByClassName("articleThumb");
}

function getDesignerLinks() {
    return document.getElementsByClassName("designerLink");
}

function onAppereanceClick(appearanceId) {
    var articleThumbs = getArticleThumbs();
    var designerLinks = getDesignerLinks();
    var length = articleThumbs.length;
    for (var i = 0; i < length; i++) {
        var articleThumb = articleThumbs[i];
        var baseUrl = articleThumb.getAttribute('baseUrl');
        articleThumb.src = baseUrl + appearanceId;

        var designerLink = designerLinks[i];
        var designerBaseUrl = designerLink.getAttribute('baseUrl');
        designerLink.href = designerBaseUrl + appearanceId;
    }
}
