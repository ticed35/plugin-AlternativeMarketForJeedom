// Point d'entrée du script
$(document).ready(function () {
    $('#sources-manager').click(function () {
        showConfigModal();
        return false;
    });
});

/**
 * Affiche la fenêtre de configuration
 */
function showConfigModal() {
    var modal = $('#md_modal');
    modal.dialog({title: 'Configuration'});
    modal.load('index.php?v=d&plugin=AlternativeMarketForJeedom.class&modal=config.AlternativeMarketForJeedom.class').dialog('open');
}
