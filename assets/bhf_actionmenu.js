jQuery(function () {
    jQuery(".files .action-menu-list").each(function () {
        let fileref_id = jQuery(this).closest("tr").attr("id").substr(8);
        let url = STUDIP.URLHelper.getURL("plugins.php/bachelorhomeworkfolderplugin/forward/message/" + fileref_id);

        let forward = jQuery("<li class='action-menu-item'><a href='" + url + "' data-dialog><img src='" + STUDIP.ASSETS_URL + "images/icons/blue/mail.svg' class='icon-role-clickable icon-shape-mail' alt='mail' width='20' height='20'> Weiterleiten</a></li>");
        forward.insertAfter(jQuery(this).find("li:nth-child(1)"));
    });
});
