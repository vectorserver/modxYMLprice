let app = {
    options: {
        savedParams: modxYMLpriced.offers.offers_key_mapping,
        tplTags: modxYMLpriced.tags,
        getAllfieldsResource: modxYMLpriced.getAllfieldsResource,

        connector: '../assets/components/modxYMLprice/connector.php',

        formApp_ID: 'offers_key_mapping_form',
        gridApp_ID: 'offers_key_mapping_modxInputs',
        notFields: ['qwe'],
        buttonAddField_ID: 'new_buildInput',
        saveSettingsButton_ID: 'saveSettings',
    },
    init: function () {
        app.addDataList();
        app.renderFields();
        app.previewSettings();

    },
    previewSettings: function () {
        let div = document.getElementById('previewSettings');
        console.log(modxYMLpriced)
        div.innerHTML=`<hr>Код для вставки: <code>[[!modxYMLprice_snippet? &parents=\`${modxYMLpriced.snippet.parents}\`]]</code>`;
    },
    addDataList: function () {
        let datalist = document.getElementById('modxInputs');
        for (let keyObj in app.options.getAllfieldsResource) {
            let itemOption = app.options.getAllfieldsResource[keyObj];
            datalist.innerHTML += `<option value="${itemOption.value}">${itemOption.name}</option>`;
        }


    },
    renderFields: function () {
        let offers = app.options.savedParams;
        document.getElementById(app.options.gridApp_ID).innerHTML = '';

        for (let field in offers) {
            let element = offers[field];

            document.getElementById(app.options.gridApp_ID).innerHTML += app.addItem(field, element);
        }

        //eventsLoad
        app.eventsLoad();

        //inits fn
        app.selectField();
        app.save();
    },
    addItem: function (field, params) {
        let dnone = (field === 'parent' || field === 'uri') ? 'd-none' : '',
            delICon = !app.options.notFields.includes(params.field) ? `<i  data-del="${field}" class="buildInput_dell">❌</i>` : '',

            tpl = `<div id="buildInput_${field}" class="input-group buildInput ${dnone}">
            ${delICon}
            <h3><a title="Подробное описание" target="_blank" href="https://yandex.ru/support/marketplace/assortment/fields/index.html#${params.field}">${params.field} 🔗 </a> - <small>${params.info}</small></h3>
            <div class="input">
            <label for="${field}">Поле ресурса или tv для связки (tv_TVNAME}), ms_FIELD (miniShop2)</label>
            
            <input list="modxInputs" autocomplete data-params='${JSON.stringify(params)}' class="inputdata x-form-text x-form-field x-form-empty-field" type="text" name="${params.field}" value="${field}" placeholder="Укажите значение!">
            </div>
        </div>`;

        return tpl;
    },
    removeItem: function (field) {
        document.getElementById('buildInput_' + field).remove();
    },
    selectField: function () {

        document.getElementById(app.options.buttonAddField_ID).onclick = function () {

            let optionsSelect = '';
            let tagsMeta = [];


            for (let keyObj in app.options.tplTags) {
                let tagData = app.options.tplTags[keyObj];
                let tag = tagData['field'];
                //let selected = tag === 'param' ? 'selected' : '';
                optionsSelect += `<option data-param='${JSON.stringify(tagData)}'>${tag}</option>`;

                tagsMeta[tag] = tagData;
            }

            let selectTpl = `<br><div class="x-form-item">
                        <div class="x-form-element">
                            <select required style="width: 100%;" class="modx-tv-legacy-select" id="approval">
                                <option selected></option>
                                ${optionsSelect}
                                </select>
                        </div>
                       </div>`;


            Ext.MessageBox.show({
                title: 'Добавить тег',
                width: '300',
                msg: '<h4>Подробнее о тегах тут: <a target="_blank" href="https://yandex.ru/support/marketplace/assortment/fields/index.html">ссылка</a></h4>' + selectTpl + '<div style="margin-top: 15px;color: rgb(111 111 111);font-family: monospace;" id="approval_desk"></div>',
                buttons: Ext.MessageBox.OKCANCEL,
                fn: function (btn, val) {

                    if (btn == 'ok') {
                        //addfield
                        let seletedOpt = Ext.get('approval');
                        let tagValue = seletedOpt.getValue();
                        //let fieldName = "tv_custom" + tagValue;
                        let fieldName = "";
                        if (!tagValue) {
                            alert('Вы ничего не выбрали!');
                            return false;
                        }

                        if (app.options.savedParams[fieldName]) {
                            alert('Поле с таким значением как ' + fieldName + ' уже существует, тег ' + tagValue + '!');
                            return false;
                        } else {
                            app.options.savedParams[fieldName] = tagsMeta[tagValue];
                            app.renderFields();
                            app.setParams();
                        }

                    }
                }
            });

            //ev
            document.getElementById('approval').addEventListener("change", function (e) {
                console.log(tagsMeta[e.target.value])
                document.getElementById('approval_desk').innerHTML = tagsMeta[e.target.value]['info'];
            });

        }

    },
    setParams: function () {
        let inputsData = document.querySelectorAll('.inputdata');

        let generatedParams = {};
        inputsData.forEach((element) => {
            let fieldResource = element.getAttribute('value');
            let fieldData = JSON.parse(element.getAttribute('data-params'));
            //ad cfg params
            generatedParams[fieldResource] = fieldData;
        });

        if (generatedParams !== app.options.savedParams) {
            app.options.savedParams = generatedParams;
            console.log('setParams', app.options.savedParams);
        }


        return app.options.savedParams;
    },
    save: function () {

        document.getElementById(app.options.saveSettingsButton_ID).onclick = function () {
            let toSaveparams = app.setParams();

            if(toSaveparams[""]){
                alert("Укажите значение в поле: "+toSaveparams[""].field+", он не может быть пустым!");
                return false;
            }

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
                    }],
                url: app.options.connector,
                baseParams: {
                    action: 'offers_key_mapping',
                    data: JSON.stringify(toSaveparams)
                },
                success: function (k, v) {
                    window.location.reload();
                }

            });
            win.show();
        }

    },
    eventsLoad: function () {

        //events remove field
        document.querySelectorAll('.buildInput_dell')
            .forEach(input => input.addEventListener('click', function (e) {
                app.removeItem(e.target.getAttribute('data-del'));
                app.setParams();
            }));

        //form changevalue
        document.getElementById(app.options.formApp_ID).addEventListener("input", function (e) {
            let nvalue = e.target.value;
            e.target.setAttribute('value', nvalue);
            app.setParams();
        });
    },
}


Ext.onReady(function () {
    app.init();
});

