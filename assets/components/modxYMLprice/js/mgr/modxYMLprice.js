let {
    addOfferList, getFields, init, removeOfferList, save,options: {
        Yml_config,
        buttonAddField,
        connector,
        formApp,
        gridApp,
        notFields,
        offers_key_mapping,
        saveSettingsButton
    }
} = {
    options: {
        MODx_config: MODx.config,
        Yml_config: modxYMLpriced || [],
        offers_key_mapping: modxYMLpriced["offers"]['offers_key_mapping'] || [],

        formApp: 'offers_key_mapping_form',
        gridApp: 'offers_key_mapping_modxInputs',
        notFields: ['name', 'description', 'picture', 'vendor', 'price'],
        buttonAddField: 'new_buildInput',
        saveSettingsButton: 'saveSettings',

        connector: '../assets/components/modxYMLprice/connector.php',
    },
    init: function () {
        getFields(offers_key_mapping);
    },
    getFields: function (offers) {
        document.getElementById(gridApp).innerHTML = '';

        for (let field in offers) {
            let element = offers[field];

            document.getElementById(gridApp).innerHTML += addOfferList(field, element);
        }

        //events remove field
        document.querySelectorAll('.buildInput_dell')
            .forEach(input => input.addEventListener('click', function (e) {
                removeOfferList(e.target.getAttribute('data-del'))
            }));

        save();

        //events add field
        document.getElementById(buttonAddField).onclick = function () {

            let options = '';
            let tagsMeta = [];

            for (let keyObj in Yml_config.tags) {
                let tagData = Yml_config.tags[keyObj];
                let tag = tagData['field'];
                let selected = tag === 'param' ? 'selected' : '';

                tagsMeta[tag] = tagData;

                options += `<option data-param='${JSON.stringify(tagData)}' ${selected}>${tag}</option>`
            }

            let select = `<br><div class="x-form-item">
                        <div class="x-form-element">
                            <select style="width: 100%;" class="modx-tv-legacy-select" id="approval">${options}</select>
                        </div>
                       </div>`;


            Ext.MessageBox.show({
                title: 'Добавить тег',
                width: '300',
                msg: '<h4>Подробнее о тегах тут: <a target="_blank" href="https://yandex.ru/support/marketplace/assortment/fields/index.html">ссылка</a></h4>' + select,
                buttons: Ext.MessageBox.OKCANCEL,
                fn: function (btn) {
                    if (btn == 'ok') {
                        //addfield
                        let seletedOpt = Ext.get('approval');
                        let tagValue = seletedOpt.getValue();
                        let fieldName = "tv_custom" + tagValue;

                        if (offers_key_mapping[fieldName]) {
                            let sizeObj = Object.keys(offers_key_mapping).length + 1;
                            fieldName = fieldName + '' + sizeObj;
                            console.log(fieldName)
                        }
                        //add cfg
                        offers_key_mapping[fieldName] = tagsMeta[tagValue];
                        getFields(offers_key_mapping);
                    }
                }
            });
        }
        //form
        document.getElementById(formApp).addEventListener("input", function (e) {
            let nvalue = e.target.value;
            e.target.setAttribute('value', nvalue);
        });
    },
    addOfferList: function (field, params) {
        let dnone = (field === 'parent' || field === 'uri') ? 'd-none' : '',
            delICon = !notFields.includes(params.field) ? `<i  data-del="${field}" class="buildInput_dell">❌</i>` : '',

            tpl = `<div id="buildInput_${field}" class="input-group buildInput ${dnone}">
            ${delICon}
            <h3><a title="Подробное описание" target="_blank" href="https://yandex.ru/support/marketplace/assortment/fields/index.html#${params.field}">${params.field} 🔗 </a> - <small>${params.info}</small></h3>
            <div class="input">
            <label for="${field}">Поле ресурса или tv для связки (tv_TVNAME}), ms_FIELD (miniShop2)</label>
            
            <input data-params='${JSON.stringify(params)}' class="inputdata x-form-text x-form-field x-form-empty-field" type="text" name="${params.field}[]" value="${field}" placeholder="${params.default}">
            </div>
        </div>`;

        return tpl;
    },
    removeOfferList: function (field) {

        //del elem
        document.getElementById('buildInput_' + field).remove();
        //del conf
        console.log()
        delete offers_key_mapping[field];
    },
    save: function () {
        document.getElementById(saveSettingsButton).onclick = function () {
            let inputsData  = document.querySelectorAll('.inputdata');
            inputsData.forEach((element) => {
                let fieldResource = element.getAttribute('value');
                let fieldData = element.getAttribute('data-params')

                //ad cfg params
                offers_key_mapping[fieldResource] = JSON.parse(fieldData);
            });

            let win = new MODx.Window({
                //id: 'my_window',
                title: 'Настройки конфига YML',
                labelAlign: 'left',
                items: [
                    {
                        xtype: 'panel',
                        flex: 1,
                        html: '<h4 style="margin-top: 15px">Перед сохранением внимательно изучите все теги!</h4>',
                        style: 'background-color: #5E99CC;'
                    },
                    /*{
                    xtype: 'combo', // Текстовое поле
                    name: 'name',
                    fieldLabel: 'Name'
                }, {
                    xtype: 'textarea', // Текстовая область
                    name: 'description',
                    fieldLabel: 'Description'
                }, {
                    xtype: 'checkbox', // Чекбокс
                    name: 'active',
                    fieldLabel: 'Active'
                }*/],
                url: connector,
                baseParams: {
                    action: 'offers_key_mapping',
                    data: JSON.stringify(offers_key_mapping)
                },
                success: function (k, v) {
                    window.location.reload();
                }

            });
            win.show();
        }
    }
}

Ext.onReady(function () {
    init()
});