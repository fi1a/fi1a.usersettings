(function ($) {
    $(function () {
        $('.js-fus-add-tab').on('click', function (event) {
            event.preventDefault();

            (new BX.CDialog({'content_url':'/bitrix/admin/fi1a_usersettings_edit_tab.php','width':'770','min_width':'700'})).Show();
        });

        $('.js-fus-add-field').on('click', function (event) {
            event.preventDefault();

            window.fusAddFieldDialog = (new BX.CDialog({'content_url':'/bitrix/admin/fi1a_usersettings_userfield_edit.php','width':'770','min_width':'700'}));
            window.fusAddFieldDialog.Show();
        });
    });
})(jQuery);