(function () {
    'use strict'; 
 
    var initParent = BX.Sale.OrderAjaxComponent.init,
        initOptionsParent = BX.Sale.OrderAjaxComponent.initOptions,
        getSelectedDeliveryParent = BX.Sale.OrderAjaxComponent.getSelectedDelivery,
        getDeliveryPriceNodesParent = BX.Sale.OrderAjaxComponent.getDeliveryPriceNodes,
        editDeliveryInfoParent = BX.Sale.OrderAjaxComponent.editDeliveryInfo,
        editPropsItemsParent = BX.Sale.OrderAjaxComponent.editPropsItems,
        getBlockFooterParent = BX.Sale.OrderAjaxComponent.getBlockFooter,
        editOrderParent = BX.Sale.OrderAjaxComponent.editOrder,
        editFadeDeliveryContentParent = BX.Sale.OrderAjaxComponent.editFadeDeliveryContent,
        isValidPropertiesBlockParent = BX.Sale.OrderAjaxComponent.isValidPropertiesBlock,
        refreshOrderParent = BX.Sale.OrderAjaxComponent.refreshOrder,
        deliveryFormInitialized = false;
 
    BX.namespace('BX.Sale.OrderAjaxComponentExt');    
 
    BX.Sale.OrderAjaxComponentExt = BX.Sale.OrderAjaxComponent;

    /****************************************************************
     * Переопределенные методы
     ***************************************************************/
 
	BX.Sale.OrderAjaxComponentExt.init = function (parameters) {
        initParent.apply(this, arguments);
        /*
        var editSteps = this.orderBlockNode.querySelectorAll('.bx-soa-editstep'), i;
        for (i in editSteps) {
            if (editSteps.hasOwnProperty(i)) {
                BX.remove(editSteps[i]);
            }
        } 
        */
    };

    BX.Sale.OrderAjaxComponentExt.initOptions = function() {
        initOptionsParent.apply(this, arguments);
        this.propertyDeliveryCollection = new BX.Sale.PropertyCollection(BX.merge({publicMode: true}, this.result.DELIVERY_PROPS));
        this.initDeliveryForm();
    };

    BX.Sale.OrderAjaxComponentExt.getSelectedDelivery=function()
    {
        let currentDelivery=getSelectedDeliveryParent.apply(this, arguments);

        if(!currentDelivery) {
            let deliveryCheckbox = this.deliveryBlockNode.querySelector('input[type=radio][name=DELIVERY_ID]:checked');
            if(deliveryCheckbox) {
                let i, deliveryId = deliveryCheckbox.value;
                for (i in this.result.DELIVERY)
                {
                    if (this.result.DELIVERY[i].ID == deliveryId)
                    {
                        currentDelivery = this.result.DELIVERY[i];
                        currentDelivery.NAME=currentDelivery.OWN_NAME;
                        break;
                    }
                }
            }
        }    
        
        if(!currentDelivery) {
            currentDelivery=this.getSelectedDeliveryByResult();
        }

        return currentDelivery;
    };

    BX.Sale.OrderAjaxComponentExt.getDeliveryPriceNodes=function(delivery)
    {
        if(!parseFloat(delivery.PRICE)) {
            delivery.PRICE_FORMATED='-';
        }
        
        if(!parseFloat(delivery.DELIVERY_DISCOUNT_PRICE)) {
            delivery.DELIVERY_DISCOUNT_PRICE_FORMATED='-';
        }
        
        return getDeliveryPriceNodesParent.apply(this, arguments);
    };

    BX.Sale.OrderAjaxComponentExt.editDeliveryItems=function(deliveryNode)
    {
        if (!this.result.DELIVERY || this.result.DELIVERY.length <= 0)
            return;

        var deliveryItemsContainer = BX.create('DIV', {props: {className: 'col-sm-7 bx-soa-pp-item-container'}}),
            chooseDeliveryConteiner = BX.create('DIV', {props: {className: 'delivery__choose'}}),
            textItemsConteiner = BX.create('DIV', {props: {className: 'delivery__text'}}),
            deliveryItemNode, k, deliveryForms=[];

            for (k = 0; k < this.deliveryPagination.currentPage.length; k++)
            {
                if(deliveryItemNode = this.createDeliveryItem(this.deliveryPagination.currentPage[k])) {
                    textItemsConteiner.appendChild(deliveryItemNode);
                    deliveryForms.push(this.createDeliveryForm(this.deliveryPagination.currentPage[k]));
                }
            }
            
        chooseDeliveryConteiner.appendChild(textItemsConteiner);
        for(k = 0; k < deliveryForms.length; k++) {
            if(deliveryForms[k]) {
                chooseDeliveryConteiner.appendChild(deliveryForms[k]);
            }
        }

        deliveryItemsContainer.appendChild(chooseDeliveryConteiner);

        if (this.deliveryPagination.show)
        this.showPagination('delivery', deliveryItemsContainer);

        deliveryNode.appendChild(deliveryItemsContainer);
    };

    BX.Sale.OrderAjaxComponentExt.createDeliveryItem=function(item) 
    {
        var checked = item.CHECKED == 'Y',
            deliveryId = parseInt(item.ID),
            itemNode;
        
        /*
        if(!(this.isDeliveryProfileNsk() || !this.isDeliveryProfileOther() || this.isDeliveryProfilePickUp())) {
            return null;
        }
        */

        let inputID='ID_DELIVERY_ID_' + deliveryId;
        let input=BX.create('INPUT', {
            props: {
                id: inputID,
                name: 'DELIVERY_ID',
                type: 'radio',
                value: deliveryId,
                checked: checked
            }
        });        
        let label=BX.create('LABEL', {
            attrs: {for: inputID},
            text: item.OWN_NAME
        });
        
        itemNode=BX.create('DIV', {
            children: [input, label],
            props: {className: 'delivery__wrap'},
            events: {click: BX.proxy(this.selectDelivery, this)}
        });
        
        return itemNode;
    };

    BX.Sale.OrderAjaxComponentExt.editDeliveryInfo=function(deliveryNode) 
    {
        editDeliveryInfoParent.apply(this, arguments);

        let currentDelivery = this.getSelectedDelivery();
        if(this.isDeliveryProfileNsk() || this.isDeliveryProfileOther()) {
            $('.bx-soa-pp-desc-container').append(this.getReciverContainer());
        }
    };

    BX.Sale.OrderAjaxComponentExt.editPropsItems=function(propsNode)
    {
        if (!this.result.ORDER_PROP || !this.propertyCollection)
            return;

        var propsItemsContainer = BX.create('DIV', {props: {className: 'col-sm-12 bx-soa-customer'}}),
            group, property, groupIterator = this.propertyCollection.getGroupIterator(), propsIterator;

        if (!propsItemsContainer)
            propsItemsContainer = this.propsBlockNode.querySelector('.col-sm-12.bx-soa-customer');

        while (group = groupIterator())
        {
            propsIterator =  group.getIterator();
            while (property = propsIterator())
            {
                if (this.isDeliveryGroup(property.getGroupId())) {
                    continue;
                }

                if (
                    this.deliveryLocationInfo.loc == property.getId()
                    || this.deliveryLocationInfo.zip == property.getId()
                    || this.deliveryLocationInfo.city == property.getId()
                )
                    continue;

                this.getPropertyRowNode(property, propsItemsContainer, false);
            }
        }

        propsNode.appendChild(propsItemsContainer);
    };

    BX.Sale.OrderAjaxComponentExt.selectDelivery=function(event)
    {
        if (!this.orderBlockNode)
            return;

        var target = event.target || event.srcElement, 
            actionInput;

        if(BX.hasClass(BX(target), 'delivery__wrap')) {
            actionInput=BX(target).querySelector('input');
        }
        else if(BX.hasClass(BX(target.parentElement), 'delivery__wrap')) {
            actionInput=BX(target.parentElement).querySelector('input');
        }
        
        if(!actionInput) {
            return;
        }

        actionInput.checked = true;
        
        this.sendRequest();
    };

    BX.Sale.OrderAjaxComponentExt.initValidation = function() {
        if (!this.result.ORDER_PROP || !this.result.ORDER_PROP.properties)
            return;

        var properties = this.result.ORDER_PROP.properties, 

            deliveryProps = this.result.DELIVERY_PROPS.properties,
            obj = {}, deliveryObj = {}, i;


        for (i in properties)
        {
            if (properties.hasOwnProperty(i))
                obj[properties[i].ID] = properties[i];
        }
        for (i in deliveryProps)
        {
            if (deliveryProps.hasOwnProperty(i))
                deliveryObj[deliveryProps[i].ID] = deliveryProps[i];
        }

        this.validation.properties = obj;
        this.validation.deliveryProperties = deliveryObj;
    };

    BX.Sale.OrderAjaxComponentExt.isValidDeliveryBlock = function(excludeLocation) {
        if (!this.options.propertyValidation)
            return [];

        var props = this.orderBlockNode.querySelectorAll('.bx-soa-customer-field[data-property-id-row]'),
            propsErrors = [],
            id, propContainer, arProperty, data, i;
        for (i = 0; i < props.length; i++)
        {
            id = props[i].getAttribute('data-property-id-row');

            if (!!excludeLocation && this.locations[id])
                continue;

            propContainer = props[i].querySelector('.soa-property-container');
            if (propContainer)
            {
                arProperty = this.validation.deliveryProperties[id];
                data = this.getValidationData(arProperty, propContainer);
                propsErrors = propsErrors.concat(this.isValidProperty(data, true));
            }
        }
        return propsErrors;
    };

    BX.Sale.OrderAjaxComponentExt.editFadeDeliveryContent = function(node) {
        editFadeDeliveryContentParent.apply(this, arguments);
        if (this.initialized.delivery) { //проверяем, была ли инициализирована доставка
            var validDeliveryErrors = this.isValidDeliveryBlock(); //вызываем наш метод
            if (validDeliveryErrors.length && BX.hasClass(BX.findParent(node),'bx-selected') == true) {
                this.showError(this.deliveryBlockNode, validDeliveryErrors);
            } else { //если ошибок нет и всё в порядке
                node.querySelector('.alert.alert-danger').style.display = 'none';

                var section = BX.findParent(node.querySelector('.alert.alert-danger'), {className: 'bx-soa-section'});

                node.setAttribute('data-visited', 'true');
                BX.removeClass(section, 'bx-step-error'); //убираем иконку, что есть ошибка в этом шаге
                BX.addClass(section, 'bx-step-completed'); //выставляем, что блок валиден и готов
            }
        }
    };

    BX.Sale.OrderAjaxComponentExt.isValidPropertiesBlock=function(excludeLocation)
    {
        if (!this.options.propertyValidation)
            return [];

        var props = this.orderBlockNode.querySelectorAll('.bx-soa-customer-field[data-property-id-row]'),
            propsErrors = [],
            id, propContainer, arProperty, data, i;

        for (i = 0; i < props.length; i++)
        {
            id = props[i].getAttribute('data-property-id-row');

            if (!!excludeLocation && this.locations[id])
                continue;

            propContainer = props[i].querySelector('.soa-property-container');
            if (propContainer)
            {
                arProperty = this.validation.properties[id];
                data = this.getValidationData(arProperty, propContainer);
                propsErrors = propsErrors.concat(this.isValidProperty(data, true));
            }
        }

        return propsErrors;
    },

    BX.Sale.OrderAjaxComponentExt.saveOrder = function(result) 
    {
        var res = BX.parseJSON(result), redirected = false;
        if (res && res.order)
        {
            result = res.order;
            this.result.SHOW_AUTH = result.SHOW_AUTH;
            this.result.AUTH = result.AUTH;

            if (this.result.SHOW_AUTH)
            {
                this.editAuthBlock();
                this.showAuthBlock();
                this.animateScrollTo(this.authBlockNode);
            }
            else
            {
                if (result.REDIRECT_URL && result.REDIRECT_URL.length)
                {
                    if (this.params.USE_ENHANCED_ECOMMERCE === 'Y')
                    {
                        this.setAnalyticsDataLayer('purchase', result.ID);
                    }

                    redirected = true;
                    document.location.href = result.REDIRECT_URL;
                }
                if (result.ERROR.hasOwnProperty('PROPERTY')) {
                    result.ERROR['DELIVERY'] = result.ERROR.PROPERTY;
                    delete result.ERROR.PROPERTY;
                }
                this.showErrors(result.ERROR, true, true);
            }
        }

        if (!redirected)
        {
            this.endLoader();
            this.disallowOrderSave();
        }
    };

    BX.Sale.OrderAjaxComponentExt.refreshOrder=function(result)
    {
        let resultError=result.error;
        let resultOrderError=result.order.ERROR;
        result.error=false;
        result.order.ERROR=[];
        let resultRefreshOrder=refreshOrderParent.apply(this, arguments);
        result.error=resultError;
        result.order.ERROR=resultOrderError;
        return resultRefreshOrder;
    };

    /****************************************************************
     * Дополнительные методы
     ***************************************************************/

    BX.Sale.OrderAjaxComponentExt.getAttrChecked=function(checked)
    {
        return checked ? 'checked="checked"' : '';
    }
    BX.Sale.OrderAjaxComponentExt.getAttrSelected=function(selected)
    {
        return selected ? 'selected="selected"' : '';
    }

    BX.Sale.OrderAjaxComponentExt.isDeliveryProfile=function(profileDeliveryId, deliveryId)
    {
        if(typeof deliveryId == 'undefined') { 
            let currentDelivery=this.getSelectedDelivery();
            if(currentDelivery) deliveryId=currentDelivery.ID;
            else return false;
        }
            
        return (+deliveryId == +profileDeliveryId);
    }

    BX.Sale.OrderAjaxComponentExt.isDeliveryProfilePickUp=function(deliveryId)
    {
        return this.isDeliveryProfile(this.params.ORDER_AJAX_EXT_PICKUP_DELIVERY_ID, deliveryId);        
    }

    BX.Sale.OrderAjaxComponentExt.isDeliveryProfileNsk=function(deliveryId)
    {
        return this.isDeliveryProfile(this.params.ORDER_AJAX_EXT_NSK_DELIVERY_ID, deliveryId);
    }

    BX.Sale.OrderAjaxComponentExt.isDeliveryProfileOther=function(deliveryId)
    {
        return this.isDeliveryProfile(this.params.ORDER_AJAX_EXT_OTHER_DELIVERY_ID, deliveryId);                
    }

    BX.Sale.OrderAjaxComponentExt.isDeliveryGroup=function(groupId)
    {
        return ($.inArray(groupId, [
            this.params.ORDER_AJAX_EXT_DELIVERY_GROUP_ID,
            this.params.ORDER_AJAX_EXT_BIZ_DELIVERY_GROUP_ID,
        ]) > -1);
    }
     
    BX.Sale.OrderAjaxComponentExt.getDeliveryPropCodes=function()
    {
        return [
            'DELIVERY_DISTRICT',
            'DELIVERY_DESIRED_DATE',
            'DELIVERY_USE_BOXING',
            'DELIVERY_IS_RECEIVER',
            'DELIVERY_RECEIVER_NAME',
            'DELIVERY_RECEIVER_DISTRICT',
            'DELIVERY_RECEIVER_ADDRESS',
            'DELIVERY_RECEIVER_PHONE',
            'DELIVERY_RECEIVER_CITY'
        ];
    };

    BX.Sale.OrderAjaxComponentExt.initDeliveryForm=function()
    {
        if(!deliveryFormInitialized) {
            // bind events
            $(document).on('click', '.js-delivery-use-boxing-field', function() {
                BX.Sale.OrderAjaxComponentExt.sendRequest();
            });

            $(document).on('click', '.js-delivery-fixed-use-boxing-field', function(e) {
                e.stopImmediatePropagation();
                return false;
            });

            $(document).on('change', '.js-delivery-district-field', function() {
                BX.Sale.OrderAjaxComponentExt.sendRequest();
            });

            $(document).on('click', '.js-delivery-is-receiver-field', function(e) {
                if($(e.target).closest(':checkbox').prop('checked')) {
                    $("[id^='deliveryformprops_']").hide();
                }
                else {
                    $("[id^='deliveryformprops_']").show();
                }

                if(BX.Sale.OrderAjaxComponentExt.isDeliveryProfileNsk()) {
                    BX.Sale.OrderAjaxComponentExt.sendRequest();
                }
                else if (BX.Sale.OrderAjaxComponentExt.isDeliveryProfileOther()) {
                    $('.js-delivery__receiver-form').toggle();
                }
            });

            $(document).on('click', '[data-datepicker]', function(e) {
                BX.calendar({
                    node: this,
                    bTime: false,
                    field: this
                });
            });

            deliveryFormInitialized=true;
        }
    };

    BX.Sale.OrderAjaxComponentExt.isDeliveryProp=function(property)
    {
        return ($.inArray(property.getSettings().CODE, this.getDeliveryPropCodes()) > -1);
    };

    BX.Sale.OrderAjaxComponentExt.getDeliveryPropertyByCode=function(code)
    {
        let group, property, propsIterator,
            groupIterator = this.propertyDeliveryCollection.getGroupIterator();

        while (group = groupIterator()) {
            propsIterator =  group.getIterator();
            while (property = propsIterator()) {
                if(property.getSettings().CODE == code) {
                    return property;
                }
            }
        }

        return null;
    };

    BX.Sale.OrderAjaxComponentExt.getDeliveryPropNameByCode=function(code)
    {
        let property=this.getDeliveryPropertyByCode(code);        
        return property ? ('ORDER_PROP_' + property.getId()) : code;
    };

    BX.Sale.OrderAjaxComponentExt.getDeliveryPropValueByCode=function(code)
    {
        let property=this.getDeliveryPropertyByCode(code);        
        return property ? property.getValue() : null;
    };
    
    BX.Sale.OrderAjaxComponentExt.getRandId=function()
    {
        return 'id' + Math.round(Math.random() * 100000);
    }

    BX.Sale.OrderAjaxComponentExt.getSelectedDeliveryByResult=function()
    {
        let i, currentDelivery=false;

        for (i in this.result.DELIVERY)
        {
            if (this.result.DELIVERY[i].CHECKED == 'Y')
            {
                currentDelivery = this.result.DELIVERY[i];
                currentDelivery.NAME=currentDelivery.OWN_NAME;
                break;
            }
        }

        return currentDelivery;
    };
    
    BX.Sale.OrderAjaxComponentExt.getDistrictSelect=function(code)
    {
        let k, item, html;
        
        html=`<select name="${this.getDeliveryPropNameByCode(code)}" class="js-delivery-district-field"><option value="">Не выбран</option>`;
        for(k in this.params.DELIVERY_NSK_DISTRICTS) {
            item=this.params.DELIVERY_NSK_DISTRICTS[k];
            html+=`<option data-remote="${item.IS_REMOTE == 'Y' ? 'Y' : 'N'}" 
                value="${item.ID}" ${this.getAttrSelected(this.getDeliveryPropValueByCode(code) == item.ID)}>${item.NAME}</option>`;
        }
        html+='</select>';

        return html; 
    }

    BX.Sale.OrderAjaxComponentExt.getDistrictContainer=function()
    {
        return `<div class="delivery__district delivery__row"><label><span>* </span> Район</label>${this.getDistrictSelect('DELIVERY_DISTRICT')}</div>`;
    }

    BX.Sale.OrderAjaxComponentExt.getDesiredDateContainer=function()
    {
        let currentDate=this.getDeliveryPropValueByCode('DELIVERY_DESIRED_DATE');
        if(!currentDate) {
            let today=new Date(), todayMonth=today.getMonth()*1 + 1, todayDate=today.getDate();
            currentDate=(todayDate < 10 ? ('0'+todayDate) : todayDate)
            + '.' + (todayMonth < 10 ? ('0'+todayMonth) : todayMonth) 
            + '.' + today.getFullYear();
        }
        
        return `<div class="delivery__date">
            <p class="js-delivery__date-label">Желаемая дата доставки</p><div class="delivery__block">
            <input data-datepicker="1" type="text" name="${this.getDeliveryPropNameByCode('DELIVERY_DESIRED_DATE')}" value="${currentDate}" />
            <button type="button"><div class="delivery__date-icon" alt="date"></div></button>
        </div></div>`;
    };

    BX.Sale.OrderAjaxComponentExt.getBoxingContainer=function(checked)
    {
        let id=this.getRandId(), value=this.getDeliveryPropValueByCode('DELIVERY_USE_BOXING');
        
        return `<div class="delivery__row delivery__check">
            <input type="checkbox" class="js-delivery-use-boxing-field" name="${this.getDeliveryPropNameByCode('DELIVERY_USE_BOXING')}" 
                value="Y" id="${id}" ${this.getAttrChecked(value == 'Y')}>
            <label for="${id}">Упаковка <i>(индивидуальная упаковка окна)</i></label>
        </div>`;
    }

    BX.Sale.OrderAjaxComponentExt.getFixedBoxingContainer=function()
    {
        return `<div class="delivery__row delivery__check">
            <input type="checkbox" class="js-delivery-fixed-use-boxing-field" name="${this.getDeliveryPropNameByCode('DELIVERY_USE_BOXING')}" 
                value="Y" checked="checked">
            <label>Упаковка <i>(индивидуальная упаковка окна)</i></label>
        </div>`;
    }

    BX.Sale.OrderAjaxComponentExt.getReciverContainer=function()
    {
        let reciverCheckboxId=this.getRandId();
        let checked=(this.getDeliveryPropValueByCode('DELIVERY_IS_RECEIVER') == 'Y');        
        let html=`<div class="delivery__col-2">
            <div class="delivery__row delivery__recip recip-1">
                <input class="js-delivery-is-receiver-field" type="checkbox" name="${this.getDeliveryPropNameByCode('DELIVERY_IS_RECEIVER')}" id="${reciverCheckboxId}" value="Y" ${this.getAttrChecked(checked)}>
                <label for="${reciverCheckboxId}">Я не являюсь получателем</label>
            </div>
            <div class="delivery__person js-delivery__receiver-form" ${checked ? '' : 'style="display:none"'}>
              <p>Данные получателя</p>
              <div class="delivery__form">
                <div class="delivery__row">
                  <label> 
                    <span>* </span>ФИО
                  </label>
                  <input type="text" name="${this.getDeliveryPropNameByCode('DELIVERY_RECEIVER_NAME')}" value="${this.getDeliveryPropValueByCode('DELIVERY_RECEIVER_NAME')}">
                </div>`;
        
        if(this.isDeliveryProfileNsk()) {
            html+=`<div class="delivery__row delivery__place">
                <label> 
                <span>* </span>Выбор района
                </label>
                ${this.getDistrictSelect('DELIVERY_RECEIVER_DISTRICT')}                  
            </div>`;
        }
        else {
            html+=`<div class="delivery__row delivery__place">
                <label> 
                <span>* </span>Город
                </label>
                <input type="text" name="${this.getDeliveryPropNameByCode('DELIVERY_RECEIVER_CITY')}" value="${this.getDeliveryPropValueByCode('DELIVERY_RECEIVER_CITY')}">
            </div>`;
        }

        html+=`<div class="delivery__row">
                <label> 
                <span>* </span>Адрес
                </label>
                <textarea name="${this.getDeliveryPropNameByCode('DELIVERY_RECEIVER_ADDRESS')}" cols="30" rows="10">${this.getDeliveryPropValueByCode('DELIVERY_RECEIVER_ADDRESS')}</textarea>
            </div>
            <div class="delivery__row">
                <label> 
                <span>* </span>Телефон
                </label>
                <input type="text" name="${this.getDeliveryPropNameByCode('DELIVERY_RECEIVER_PHONE')}" value="${this.getDeliveryPropValueByCode('DELIVERY_RECEIVER_PHONE')}">
            </div>
            </div>
        </div>
        </div>`;

        return html;
    };

    BX.Sale.OrderAjaxComponentExt.createDeliveryForm=function(item)
    {
        if(item.CHECKED == 'Y') {
            let deliveryId = parseInt(item.ID);
            let container = null;
            let isReceiver = (this.getDeliveryPropValueByCode('DELIVERY_IS_RECEIVER') == 'Y');
            let deliveryPropsContainer=$(`<div id="deliveryformprops_${deliveryId}"${isReceiver?' style="display:none"':''}></div>`);

            if(this.isDeliveryProfileNsk(deliveryId)) {
                deliveryPropsContainer.append(this.getDistrictContainer());
                container=$(`<div id="deliveryform_${deliveryId}">
                    ${this.getDesiredDateContainer()}
                    ${this.getBoxingContainer()}
                    </div>`);
            }
            else if(this.isDeliveryProfileOther(deliveryId)) {
                container=$(`<div id="deliveryform_${deliveryId}">
                    ${this.getDesiredDateContainer()}
                    ${this.getFixedBoxingContainer()}
                    </div>`);

                container.find('.js-delivery__date-label').text('Желаемая дата доставки (согласовывается с менеджером)');
            }

            if(container) {
                this.appendDeliveryProps(deliveryPropsContainer[0]);
                container.append(deliveryPropsContainer);
                return container[0];
            }
        }

        return null;
    };

    BX.Sale.OrderAjaxComponentExt.appendDeliveryProps=function(container)
    {
        let group, property, propsIterator,
            groupIterator = this.propertyDeliveryCollection.getGroupIterator();

        while (group = groupIterator())
        {
            propsIterator =  group.getIterator();
            while (property = propsIterator())
            {
                if (!this.isDeliveryGroup(property.getGroupId())) {
                    continue;
                }

                if(this.isDeliveryProp(property)) {
                    continue;
                }

                if (
                    this.deliveryLocationInfo.loc == property.getId()
                    || this.deliveryLocationInfo.zip == property.getId()
                    || this.deliveryLocationInfo.city == property.getId()
                ) 
                {
                    continue;
                }

                if(property.getSettings().CODE == 'CITY') {
                    if(this.isDeliveryProfileNsk()) {
                        continue;
                    }
                }
                
                property.isRequired=function() { return true; };

                this.getPropertyRowNode(property, container, false);
            }
        }
    };

})();