(function() {
'use strict';

window.KonturCalculatorComponent=class 
{
    constructor(params) {
        this.params=params;
    }

    static create(params) {
        let component=new KonturCalculatorComponent(params);
        component.run();
        return component;
    }

    param(name, def) {
        return !!this.params[name] ? this.params[name] : def;
    }

    /**
     * Запуск приложения
     */
    run() {
        this.registerComponets();        
        this.vue=BX.Vue.create({
            el: '#' + this.param('application_id'),
            data: {
                // @var int nextItemId следующий идентификатор позиции
                nextItemId: 1,
                // @var array items массив текущих позиций в форме
                items: [],
                // @var array settingsItems массив настроек номенклатур
                settingsItems: this.param('settings_items', []),
                webformId: this.param('webform_id'),
                webformOrderFieldName: this.param('webform_order_field_name'),
                consentUrl: this.param('consent_url'),
                totalPrice: 0,             
                totalMass: 0
            },
            created: function() {
                this.$root.$on('additem', this.addItem);
                this.$root.$on('removeitem', this.removeItem);
                this.$root.$on('calc', this.calc);
            },
            mounted: function() {
                this.$root.$emit('additem');             
            },
            methods: {
                /**
                 * Разбор выражения
                 * 
                 * @var string expr выражение для разбора
                 * @return object возвращает объект вида {
                 *  vars: набор переменных из выражения, 
                 *  expression: исходное выражение
                 * }
                 */
                parseExpression: function(expression) {
                    return !!expression ? {
                        vars: Array.from(expression.matchAll(/\b[A-Z][0-9]*\b/g), m => m[0]),
                        expression: expression
                    } : {vars: [], expression: ''};
                },

                // добавление новой позиции в список
                addItem: function (event) {
                    this.items.push({
                        id: this.nextItemId++,
                        nomenclature: '',
                        fields: [],
                        price: 0,
                        mass: 0
                    });
                },

                // удаление позиции из списка
                removeItem: function(id) {
                    // this.items.splice(index, 1);
                    this.items = this.items.filter(item => item.id !== id);
                    this.calc();
                },

                // итоговый расчет
                calc: function() {
                    let totalPrice=0, totalMass=0;
                    _.each(this.items, item => {
                        totalPrice=isNaN(totalPrice) ? '-' : (isNaN(+item.price) ? '-' : (totalPrice + +item.price));
                        totalMass=isNaN(totalMass) ? '-' : (isNaN(+item.mass) ? '-' : (totalMass + +item.mass));
                    });
                    this.totalPrice=BX.Currency.currencyFormat(totalPrice, 'RUB', true);
                    this.totalMass=_.round(totalMass, 3);
                },

                onOrderClick: function(event) {
                    if(this.webformId) {
                        let webformOrderFieldSelector=`[name='${this.webformOrderFieldName}']`;
                        let n=1, plainTotalPrice=this.totalPrice.replace(/\&nbsp;/g, ' ');
                        let orderText=_.reduce(this.items, (txt, item) => {
                            return (txt.length ? `${txt}\n` : '') 
                                + `${n++}) ${_.get(item.nomenclature, 'label')} (`
                                + _.toLower(_.reduce(item.fields, (ftxt, field) => { 
                                    return (ftxt.length ? `${ftxt}, ` : '') + `${field.label}: ${field.calcValue}`; 
                                })) 
                                + `) ${item.mass}т - ${item.price} руб;`
                        }, `Общий вес: ${this.totalMass} т\nИтоговая стоимость: ${plainTotalPrice}\nПозиции заказа:`);
                        
                        let orderHtml='';                        

                        universe.forms.show({
                            'id': this.webformId, 
                            'template': '.default', 
                            'parameters': {
                                'AJAX_OPTION_ADDITIONAL': `i-20-intec-universe-widget-web-form-${this.webformId}-${_.random(99999999999)}_FORM`, 
                                'CONSENT_URL': this.consentUrl
                            }, 
                            'settings': {
                                'title': 'Оформить заказ'
                            }
                        }, function(popup, options, settings) {
                            if($(this.contentContainer).find(webformOrderFieldSelector).length) {
                                $(this.contentContainer).find(webformOrderFieldSelector)
                                    .attr('readonly', 'readonly')
                                    .css('font-size', '0.85em')
                                    .css('line-height', '1em')
                                    .css('white-space', 'pre-line')
                                    .val(orderText);
                            }
                            else {
                                $(this.contentContainer).before(orderHtml);
                            }
                        });
                    }
                }                
            },
            template: `<div class="calculator__form">
                <div class="calculator__form-items">
                    <component :is="'calc-formitem'" v-for="item in items" :key="item.id" :item="item"></component>
                </div>
                <div class="calculator__form-controls">
                    <button @click="addItem" class="intec-button intec-button-lg intec-button-cl-common">Добавить еще позицию</button>
                </div>
                <div class="calculator__form-info">
                    <div class="calculator__form-info-total-price" v-html="totalPrice"></div>
                    <div class="calculator__form-info-total-mass">{{ totalMass }}</div>
                </div>
                <div class="calculator__form-submit">
                    <button @click="onOrderClick" class="intec-button intec-button-lg intec-button-cl-common">Заказать</button>
                </div>
            </div>`
        });
    }

    /**
     * Регистрация дополнительных Vue-компонентов приложения
     */
    registerComponets() 
    {   
        // Компонент позиции формы
        BX.Vue.component('calc-formitem', {
            props: ['item'],
            data: vm => ({
                settings: null,
                nomenclatures: _.mapValues(this.param('settings_items', {}), item => ({value: item.ID, label: item.NAME})),
                fields: [],
                systemFields: [],
                nomenclature: null,
                totalPrice: 0,
                totalMass: 0,
                currency: 'руб',
                fieldsCache: {},
                systemFieldsCache: {},
            }),
            created: function() {
                this.$on('calc', this.calc);
            },
            mounted: function() {
                this.nomenclature=_.first(_.keys(this.nomenclatures));
                this.item.nomenclature=_.get(this.nomenclatures, this.nomenclature);
            },
            watch: {
                // выбор номенклатуры
                nomenclature: function(nomenclature) {
                    this.nomenclature=nomenclature;
                    this.settings=null;
                    
                    let fields=[];
                    if(_.has(this.fieldsCache, this.nomenclature)) {
                        this.settings=this.$parent.settingsItems[this.nomenclature];
                        fields=_.get(this.fieldsCache, this.nomenclature);
                        _.each(fields, field => field.value='');
                    }
                    else if(_.has(this.$parent.settingsItems, this.nomenclature)) {
                        this.settings=this.$parent.settingsItems[this.nomenclature];
                        _.each(_.get(this.settings, 'FIELDS.LIST', {}), item => fields.push({
                            key: this.nomenclature + '_' + item.VAR + item.VAR_N,
                            var: item.VAR + item.VAR_N,
                            type: 'select',
                            sort: +item.SORT,
                            label: item.NAME,
                            values: item.VALUES,
                            value: '',
                            calcValue: 0
                        }));
                        _.each(_.get(this.settings, 'FIELDS.USER', {}), item => fields.push({
                            key: this.nomenclature + '_' + item.VAR + item.VAR_N,
                            var: item.VAR + item.VAR_N,
                            type: 'number',
                            sort: +item.SORT,
                            label: item.NAME,
                            default: isNaN(+item.DEFAULT) ? 0 : +item.DEFAULT,
                            value: isNaN(+item.DEFAULT) ? 0 : +item.DEFAULT,
                            calcValue: isNaN(+item.DEFAULT) ? 0 : +item.DEFAULT,
                            isFloat: (+item.TYPE === 200), // Helper::PROP_FORM_FIELD_TYPE_FLOAT
                            placeholder: _.toString(_.toInteger(item.DEFAULT))
                        }));                        
                        fields=fields.sort((a,b) => a.sort - b.sort);
                        this.fieldsCache[this.nomenclature]=fields;
                    }

                    let systemFields=[];
                    if(_.has(this.systemFieldsCache, this.nomenclature)) {
                        this.settings=this.$parent.settingsItems[this.nomenclature];
                        systemFields=_.get(this.systemFieldsCache, this.nomenclature);
                        _.each(systemFields, field => field.value='');
                    }
                    else if(_.has(this.$parent.settingsItems, this.nomenclature)) {
                        this.settings=this.$parent.settingsItems[this.nomenclature];
                        _.each(_.get(this.settings, 'FIELDS.SYSTEM', {}), item => systemFields.push({
                            key: this.nomenclature + '_' + item.VAR + item.VAR_N,
                            var: item.VAR + item.VAR_N,
                            type: 'number',
                            default: isNaN(+item.DEFAULT) ? 0 : +item.DEFAULT,
                            isFloat: (+item.TYPE === 200), // Helper::PROP_FORM_FIELD_TYPE_FLOAT
                        }));
                        this.systemFieldsCache[this.nomenclature]=systemFields;
                    }                    

                    this.systemFields=systemFields;
                    this.fields=fields;
                    this.item.fields=this.fields;
                    this.item.nomenclature=_.get(this.nomenclatures, this.nomenclature);
                }
            },
            methods: {
                // удаление позиции
                removeItem: function() {
                    this.$root.$emit('removeitem', this.item.id);
                },

                getVarType: function(varname) {
                    let fields=this.fields.filter(field => { return field.var == varname; });
                    if(fields.length) {
                        let field=fields.pop();
                        if(_.has(field, 'isFloat') && !field.isFloat) {
                            return 'decimal';
                        }
                    }
                    return 'float';
                },

                // получение значения, после преобразования по типу переменной
                prepareValue: function(varname, value) {
                    return (this.getVarType(varname) == 'decimal')
                        ? Math.ceil(isNaN(+value) ? 0 : +value)
                        : isNaN(+value) ? 0 : +value;
                },

                // расчет позиции
                // @var primaryField поле, которое инициализировало пересчет
                calc: function(primaryField) {
                    // @var object expressions выражения для расчета переменных вида {переменная: выражение}
                    let auto={};
                    let matrix={};
                    let systems={};
                    let totalPriceExpression={};
                    let totalMassExpression={};

                    // разбор основных формул
                    totalPriceExpression=this.$root.parseExpression(_.get(this.settings, 'FORMULAS.BASE.PRICE.EXPRESSION'));
                    totalMassExpression=this.$root.parseExpression(_.get(this.settings, 'FORMULAS.BASE.MASS.EXPRESSION'));

                    // разбор формул авторасчета
                    _.forIn(_.get(this.settings, 'FORMULAS.AUTO', {}), (data, varname) => {
                        _.set(auto, varname, this.$root.parseExpression(_.get(data, 'EXPRESSION', '')));
                    });

                    // разбор формул матрицы дополнительных значений
                    _.forIn(_.get(this.settings, 'FORMULAS.MATRIX', {}), (data, varname) => {
                        _.forIn(data, (valueData, valueHash) => {
                            _.forIn(valueData, (relData, relVarName) => {
                                _.set(
                                    matrix, 
                                    `${relVarName}.${varname}.${valueHash}`, 
                                    +_.get(relData, 'EXPRESSION', '')
                                    // this.$root.parseExpression(_.get(relData, 'EXPRESSION', ''))
                                );
                            });
                        });
                    });

                    // заполняем системные свойства
                    _.forIn(this.systemFields, field => {
                        _.set(systems, field.var, field.default);
                    });
                    
                    
                    // console.log(expressions, matrix, totalPriceExpression, totalMassExpression);

                    function isCorrectExpression(expression) {
                        return !/[*\-+\/.]+\s+[*\-+\/.]+/g.test(expression);
                    }

                    let evalExpressionBreakCount=0;

                    let fields=this.fields;
                    function getListValues() {
                        let values={};
                        let selectFields=fields.filter(field => field.type == 'select');
                        if(selectFields.length > 0) {
                            _.each(selectFields, selectField => {
                                _.set(values, selectField.var, selectField.calcValue);
                            })
                        }
                        return values;
                    }

                    function getNumberValues() {
                        let values={};
                        let numberFields=fields.filter(field => field.type == 'number');
                        if(numberFields.length > 0) {
                            _.each(numberFields, numberField => {
                                if(numberField.calcValue !== '') {
                                    _.set(values, numberField.var, numberField.calcValue);
                                }
                            })
                        }
                        return values;
                    }

                    let listValues=getListValues();
                    let numberValues=getNumberValues();
                    let prepareValue=this.prepareValue;
                    let _this=this;
                    function evalExpression(exprData) {
                        evalExpressionBreakCount++;
                        // защита от бесконечной рекурсии при неверно заданных формулах
                        if(evalExpressionBreakCount > 100) { return false; }
                        let vars= exprData.vars, matrixValue=false;
                        if(exprData.vars.length > 0) {
                            // если есть переменные, то вычисляем выражение
                            _.each(exprData.vars, varname => {
                                // ищем в автозаполняемых свойствах
                                if(_.has(auto, varname)) {
                                    if(_.has(numberValues, varname)) {
                                        exprData.vars=_.filter(exprData.vars, _varname => _varname != varname);
                                        exprData.expression=_.replace(
                                            exprData.expression, 
                                            new RegExp('\\b'+varname+'\\b', 'g'), 
                                            prepareValue(varname, isNaN(+numberValues[varname]) ? 0 : numberValues[varname])
                                        );
                                    }
                                    else {
                                        exprData.vars=_.filter(exprData.vars, _varname => _varname != varname);
                                        exprData.expression=_.replace(
                                            exprData.expression,
                                            new RegExp('\\b'+varname+'\\b', 'g'), 
                                            evalExpression(auto[varname])
                                        );
                                    }
                                }
                                else if(_.has(matrix, varname)) {
                                    matrixValue=false;
                                    _.forIn(_.get(matrix, varname), (valueData, relVarname) => {
                                        if(_.has(listValues, relVarname)) {
                                            let valueHash=md5(listValues[relVarname]);
                                            if(_.has(valueData, valueHash)) {
                                                matrixValue=isNaN(+valueData[valueHash]) ? 0 : +valueData[valueHash];                                                
                                            }
                                        }
                                    });
                                    
                                    if(matrixValue === false) {
                                        matrixValue=0;
                                        _.each(_this.systemFields, systemField => {
                                            if(systemField.var == varname)  { 
                                                matrixValue=systemField.default; 
                                            }
                                        });
                                    }

                                    exprData.vars=_.filter(exprData.vars, _varname => _varname != varname);
                                    exprData.expression=_.replace(
                                        exprData.expression, 
                                        new RegExp('\\b'+varname+'\\b', 'g'), 
                                        matrixValue
                                    );
                                }
                                else if(_.has(systems, varname)) {
                                    exprData.vars=_.filter(exprData.vars, _varname => _varname != varname);
                                    exprData.expression=_.replace(
                                        exprData.expression, 
                                        new RegExp('\\b'+varname+'\\b', 'g'), 
                                        _.get(systems, varname, 0)
                                    );
                                }
                            });

                            return evalExpression(exprData);
                        }
                        else {
                            let expression=_.replace(exprData.expression, /[^0-9*\-+()\/. ]+/g, '');
                            if(isCorrectExpression(expression)) {
                                try {
                                    return _.round(eval(expression), 2);
                                }
                                catch(e) {
                                    return 0;
                                }
                            }
                        }
                    }

                    if( _.has(auto, primaryField.var)) {
                        let autoCalculatedVars=[];
                        // сбрасываем все значения вычисляемых полей
                        // и заполняем массив вычисляемых переменных
                        _.each(this.fields, field => { 
                            if((field.var != primaryField.var) && _.has(auto, field.var)) {
                                field.calcValue=0; 
                                autoCalculatedVars.push(field.var);
                            }
                        });
                        
                        function autoCaluculate(fields, autoExprData, autoCalculated) {
                            let recalculated={};
                            _.forIn(autoExprData, (exprData, varname) => {
                                if(varname != primaryField.var) {
                                    // если формула содержит другие переменные авторасчета, откладываем расчет поля
                                    if(_.intersection(exprData.vars, autoCalculatedVars).length > 0) {
                                        _.set(recalculated, varname, exprData);
                                    }
                                    else {
                                        let idx=_.findIndex(fields, f => f.var == varname);
                                        if(idx > -1) {
                                            // если формула содержит авто-переменные, которые еще не были расчитаны
                                            // то для начала вычисляем переменные в формуле
                                            if((_.difference(exprData.vars, autoCalculated).length > 0) || (_.difference(autoCalculated, exprData.vars).length > 0)) {
                                                fields[idx].calcValue=prepareValue(fields[idx].var, evalExpression(exprData));
                                                autoCalculated.push(varname);
                                                _.remove(autoCalculatedVars, _var => { return _var == varname; });
                                                // обновляем данные в numberValues
                                                numberValues=getNumberValues();
                                            }                                    
                                            else {
                                                _.set(recalculated, varname, exprData);
                                            }
                                        }
                                    }
                                }
                            });

                            if(_.keys(recalculated).length > 0) {
                                autoCaluculate(fields, recalculated, autoCalculated);
                            }
                        }

                        // производим расчет автополей, кроме активного
                        autoCaluculate(this.fields, auto, [primaryField.var]);

                        // заполняем значения полей
                        _.each(this.fields, field => field.value=field.calcValue);

                        // производим повторный перерасчет автополей для базового автополя
                        // определяем, есть ли базовое автополе для перерасчета (это первое поле с целым типом)
                        let autoBaseVar=null;
                        _.forIn(auto, (exprData, varname) => { 
                            if(!autoBaseVar && (this.getVarType(varname) == 'decimal')) autoBaseVar=varname; 
                        });
                        if(autoBaseVar && (autoBaseVar != primaryField.var)) {
                            autoCalculatedVars=[];
                            _.each(this.fields, field => { 
                                if((field.var != autoBaseVar) && _.has(auto, field.var)) {
                                    field.calcValue=0;
                                    autoCalculatedVars.push(field.var);
                                }
                                else if(field.var == autoBaseVar) {
                                    primaryField=field;
                                }
                            });
                            // переинициализация формул расчета
                            auto={};
                            _.forIn(_.get(this.settings, 'FORMULAS.AUTO', {}), (data, varname) => {
                                _.set(auto, varname, this.$root.parseExpression(_.get(data, 'EXPRESSION', '')));
                            });
                            numberValues=getNumberValues();
                            autoCaluculate(this.fields, auto, [primaryField.var]);
                        }
                    }

                    this.totalPrice=evalExpression(totalPriceExpression);
                    this.totalMass=evalExpression(totalMassExpression);
                    this.item.price=this.totalPrice;
                    this.item.mass=this.totalMass;
                    
                    this.$root.$emit('calc');
                }
            },
            template: `<div class="calculator__form-item">
                <div class="calculator__form-item_header">
                    <div class="calculator__form-item_header-left">
                        <select v-model="nomenclature">
                            <option disabled value="">-- выберите номенклатуру --</option>
                            <option v-for="nomenclature in nomenclatures" :value="nomenclature.value">{{ nomenclature.label }}</option>
                        </select>
                    </div>                        
                    <div class="calculator__form-item_header-right">
                        <a href="javascript:;" @click="removeItem">Удалить позицию</a>
                    </div>
                </div>
                <div class="calculator__form-item_fields">
                    <div class="calculator__form-item_field" v-for="field in fields" :key="field.key">
                        <label>
                            {{ field.label }}
                            <component :is="'calc-field-' + field.type" :field="field"></component>
                        </label>
                    </div>
                    <div class="calculator__form-item_summary" :class="{hidden: !nomenclature}">
                        {{ totalPrice }} <span>{{ currency }}</span>
                    </div>
                </div>
            </div>`           
        });

        BX.Vue.component('calc-field-select', {
            props: ['field'],
            data: vm => ({
                selected: '',
                isError: false
            }),
            mounted: function() {
                this.selected=_.first(this.field.values);
                this.field.value=this.selected;
                this.field.calcValue=this.field.value;
                this.$parent.$emit('calc', this.field);
            },
            watch: {
                selected: function(val) {
                    this.field.value=val;
                    this.field.calcValue=this.field.value;
                    this.$parent.$emit('calc', this.field);
                }
            },
            template: `<select v-model="selected" :class="{error: isError}">
                <option disabled value=""></option>
                <option v-for="value in field.values" :value="value">{{ value }}</option>
            </select>`
        });

        BX.Vue.component('calc-field-number', {
            props: ['field'],
            data: vm => ({
                isError: false
            }),
            mounted: function() {
                if(this.field.default) {
                    this.$parent.$emit('calc', this.field);
                }
            },
            methods: {
                onInput: function(event) {
                    this.field.value=this.$parent.prepareValue(this.field.var, event.target.value);
                    this.field.calcValue=this.field.value;
                    this.$parent.$emit('calc', this.field);
                }
            },
            template: `<div class="calculator__form-item_field-number">
                <input @input.stop.number="onInput" :value="field.value" type="input" :class="{error: isError}" :placeholder="field.placeholder">
                <span class="calculator__form-item_field-number-hint" title="значение, которое используется для расчета">{{ field.calcValue }}</span>
            </div>`
        });
    }
}
})();
