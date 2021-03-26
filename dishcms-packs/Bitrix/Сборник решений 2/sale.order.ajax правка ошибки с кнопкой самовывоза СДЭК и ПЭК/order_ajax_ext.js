(function () {
    'use strict'; 
 
    var editDeliveryInfoParent = BX.Sale.OrderAjaxComponent.editDeliveryInfo;
 
    BX.namespace('BX.Sale.OrderAjaxComponentExt');    
 
    BX.Sale.OrderAjaxComponentExt = BX.Sale.OrderAjaxComponent;

    /****************************************************************
     * Переопределенные методы
     ***************************************************************/
 
    BX.Sale.OrderAjaxComponentExt.editDeliveryInfo = function(deliveryNode)
	{
			if (!this.result.DELIVERY)
				return;

			var deliveryInfoContainer = BX.create('DIV', {props: {className: 'col-sm-5 bx-soa-pp-desc-container'}}),
				currentDelivery, logotype, name, logoNode,
				subTitle, label, title, price, period,
				clear, infoList, extraServices, extraServicesNode;

			BX.cleanNode(deliveryInfoContainer);
			currentDelivery = this.getSelectedDelivery();

			logoNode = BX.create('DIV', {props: {className: 'bx-soa-pp-company-image'}});
			logotype = this.getImageSources(currentDelivery, 'LOGOTIP');
			if (logotype && logotype.src_2x)
			{
				logoNode.setAttribute('style',
					'background-image: url(' + logotype.src_1x + ');' +
					'background-image: -webkit-image-set(url(' + logotype.src_1x + ') 1x, url(' + logotype.src_2x + ') 2x)'
				);
			}
			else
			{
				logotype = logotype && logotype.src_1x || this.defaultDeliveryLogo;
				logoNode.setAttribute('style', 'background-image: url(' + logotype + ');');
			}

			name = this.params.SHOW_DELIVERY_PARENT_NAMES != 'N' ? currentDelivery.NAME : currentDelivery.OWN_NAME;

			if (this.params.SHOW_DELIVERY_INFO_NAME == 'Y')
				subTitle = BX.create('DIV', {props: {className: 'bx-soa-pp-company-subTitle'}, text: name});

			label = BX.create('DIV', {
				props: {className: 'bx-soa-pp-company-logo'},
				children: [
					BX.create('DIV', {
						props: {className: 'bx-soa-pp-company-graf-container'},
						children: [logoNode]
					})
				]
			});
			title = BX.create('DIV', {
				props: {className: 'bx-soa-pp-company-block'},
				children: [
					BX.create('DIV', {props: {className: 'bx-soa-pp-company-desc'}, html: currentDelivery.DESCRIPTION}),
					currentDelivery.CALCULATE_DESCRIPTION
						? BX.create('DIV', {props: {className: 'bx-soa-pp-company-desc'}, html: currentDelivery.CALCULATE_DESCRIPTION})
						: null,
					BX.create('DIV', {attrs: {id: 'sale_order_ajax_pickup_button_div_pecom'}, props: {className: 'bx-soa-pp-company-desc'}, style: {
						display: (currentDelivery.ID == 21) ? 'block' : 'none'
					}}),
					BX.create('DIV', {attrs: {id: 'sale_order_ajax_pickup_button_div_cdek'}, props: {className: 'bx-soa-pp-company-desc'}, style: {
						display: (currentDelivery.ID == 54) ? 'block' : 'none'
					}})
				]
			});

			if (currentDelivery.PRICE >= 0)
			{
				price = BX.create('LI', {
					children: [
						BX.create('DIV', {
							props: {className: 'bx-soa-pp-list-termin'},
							html: this.params.MESS_PRICE + ':'
						}),
						BX.create('DIV', {
							props: {className: 'bx-soa-pp-list-description'},
							children: this.getDeliveryPriceNodes(currentDelivery)
						})
					]
				});
			}

			if (currentDelivery.PERIOD_TEXT && currentDelivery.PERIOD_TEXT.length)
			{
				period = BX.create('LI', {
					children: [
						BX.create('DIV', {props: {className: 'bx-soa-pp-list-termin'}, html: this.params.MESS_PERIOD + ':'}),
						BX.create('DIV', {props: {className: 'bx-soa-pp-list-description'}, html: currentDelivery.PERIOD_TEXT})
					]
				});
			}

			clear = BX.create('DIV', {style: {clear: 'both'}});
			infoList = BX.create('UL', {props: {className: 'bx-soa-pp-list'}, children: [price, period]});
			extraServices = this.getDeliveryExtraServices(currentDelivery);

			if (extraServices.length)
			{
				extraServicesNode = BX.create('DIV', {
					props: {className: 'bx-soa-pp-company-block'},
					children: extraServices
				});
			}

			deliveryInfoContainer.appendChild(
				BX.create('DIV', {
					props: {className: 'bx-soa-pp-company'},
					children: [subTitle, label, title, clear, extraServicesNode, infoList]
				})
			);
			deliveryNode.appendChild(deliveryInfoContainer);

			if (this.params.DELIVERY_NO_AJAX != 'Y')
				this.deliveryCachedInfo[currentDelivery.ID] = currentDelivery;
		};
})();