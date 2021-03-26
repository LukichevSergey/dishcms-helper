var KonturCoreTools_DOM={
    /**
     * @link http://stackoverflow.com/a/35385518
     * @param {String} HTML representing a single element
     * @return {Element}
     */
    toElement: function(html) {
        var template = document.createElement('template');
        template.innerHTML = html;
        return template.content.firstChild;
    }

    /**
     * @link http://stackoverflow.com/a/35385518
     * @param {String} HTML representing any number of sibling elements
     * @return {NodeList} 
     */
    toElements: function (html) {
        var template = document.createElement('template');
        template.innerHTML = html;
        return template.content.childNodes;
    }
}