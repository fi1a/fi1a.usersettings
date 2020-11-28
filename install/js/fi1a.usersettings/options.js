(function ($) {
    $(function () {
        var app = BX.Vue.create({
            el: '#fi1a-usersettings-options',
            data() {
                return $('#fi1a-usersettings-options').data('object');
            },
            computed: {
                /**
                 * Возвращает отсортированные табы
                 *
                 * @return {Array}
                 */
                sortedTabs () {
                    return this.tabs.sort(function (a, b) {
                        if (a.SORT === b.SORT) {
                            return a.ID - b.ID;
                        }

                        return a.SORT - b.SORT;
                    });
                }
            },
            methods: {
                /**
                 * Возвращает отфильтрованные и отсортированные поля по идентификатору таба
                 *
                 * @param tabId
                 *
                 * @return {Array}
                 */
                getFields(tabId) {
                    return this.fields.filter(function (field) {
                        return field.TAB_ID === tabId;
                    }).sort(function (a, b) {
                        if (a.UF.SORT === b.UF.SORT) {
                            return a.ID - b.ID;
                        }

                        return a.UF.SORT - b.UF.SORT;
                    });
                },
                /**
                 * Возвращает количество полей по идентификатору таба
                 *
                 * @param tabId
                 *
                 * @return {number}
                 */
                getFieldsCount(tabId) {
                    return this.fields.filter(function (field) {
                        return field.TAB_ID === tabId;
                    }).length;
                },
                /**
                 * Окно добавления вкладки
                 */
                addTab() {
                    (new BX.CDialog({'content_url':'/bitrix/admin/fi1a_usersettings_edit_tab.php','width':'770','min_width':'700'})).Show();
                },
                /**
                 * Окно добавления поля
                 */
                addField() {
                    window.fusAddFieldDialog = (new BX.CDialog({'content_url':'/bitrix/admin/fi1a_usersettings_userfield_edit.php','width':'770','min_width':'700'}));
                    window.fusAddFieldDialog.Show();
                },
                /**
                 * Js для контекстного меню вкладки
                 *
                 * @param {Array} tab
                 *
                 * @return {string}
                 */
                tabContextMenu(tab) {
                    let location = new URL(window.location);

                    location.searchParams.delete('tabDelete');
                    location.searchParams.append('tabDelete', tab.ID);

                    return "return [{'GLOBAL_ICON':'adm-menu-delete','TEXT':'" + this.loc.DELETE + "','ONCLICK':'if(confirm(\\'" + this.loc.DELETE_CONFIRM + "\\')) window.location=\\'" + location.href + "\\';'}];";
                },
                /**
                 * Js для контекстного меню поля
                 *
                 * @param {Array} field
                 *
                 * @return {string}
                 */
                fieldContextMenu(field) {
                    let location = new URL(window.location);

                    location.searchParams.delete('fieldDelete');
                    location.searchParams.append('fieldDelete', field.ID);

                    return "return [{'DEFAULT':true,'GLOBAL_ICON':'adm-menu-edit','DEFAULT':true,'TEXT':'" + this.loc.EDIT + "','ONCLICK':'window.fusAddFieldDialog = (new BX.CDialog({\\'content_url\\':\\'/bitrix/admin/fi1a_usersettings_userfield_edit.php?ID=" + field.ID + "\\',\\'width\\':\\'770\\',\\'min_width\\':\\'700\\'})); window.fusAddFieldDialog.Show();'}, {'GLOBAL_ICON':'adm-menu-delete','TEXT':'" + this.loc.DELETE + "','ONCLICK':'if(confirm(\\'" + this.loc.DELETE_CONFIRM + "\\')) window.location=\\'" + location.href + "\\';'}];"
                },
                /**
                 * Возвращает описание типа пользовательского поля
                 *
                 * @param {String} userTypeId
                 * @param {Array} userType
                 *
                 * @return {*}
                 */
                getUfDescription(userTypeId, userType) {
                    if (userType) {
                        return userType.DESCRIPTION;
                    }

                    return userTypeId;
                }
            }
        });
    });
})(jQuery);