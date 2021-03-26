document.addEventListener("DOMContentLoaded", function(event) { 
  
    // get the elements
    var modal = document.querySelector('.formModal');
    var btn_close;
    var father;
    var btn_showModal = document.querySelectorAll('.showModal');

    if(!!modal){		

        for (var i = 0; i < btn_showModal.length; i++) {
            //console.log(payment_wrap[i]);

            btn_showModal[i].onclick = function(){
                father = this.parentNode;

                modal = father.querySelector('.formModal');
                modal.style.display = "block";

                btn_close = father.querySelector('.closeModal');

                btn_close.onclick = function(){
                      modal.style.display = "none";
                }

            };
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                    modal.style.display = "none";
            }
        }

    }
    
    var form_payment = document.querySelectorAll('.ki-yaka form');
    for (var j = 0; j < form_payment.length; j++) {
        form_payment[j].addEventListener('submit', validate, false);
    }
  
});

function validate(e){
    
    var form = e.target;
    
    e.preventDefault();
    
    var formIsValid = true;
    var input_user_name = form.querySelector('[name="user_name"]');
    var input_user_email = form.querySelector('[name="user_email"]');
    
    if( input_user_name.value != '' ){
        input_user_name.classList.remove("error");
    }else{
        input_user_name.classList.add("error");
        formIsValid = false;
    }
    
    if( isValidEmailAddress( input_user_email.value ) ){
        input_user_email.classList.remove("error");
    }else{
        input_user_email.classList.add("error");
        formIsValid = false;
    }
    
    if( formIsValid ) getOrderID(form);
}

function getOrderID(form) {
    
    var xmlhttp = new XMLHttpRequest();
    
    var input_user_email = form.querySelector('[name="user_email"]');
    var input_quantity = form.querySelector('[name="f_quantity"]');
    var input_tax = form.querySelector('[name="f_tax"]');
    var input_sum = form.querySelector('[name="sum"]');
    var input_order_content = form.querySelector('[name="order_content"]');
    var input_ym_merchant_receipt = form.querySelector('[name="ym_merchant_receipt"]');
    
    var objCheck = {
        'customerContact' : input_user_email.value,
        'items' :[{
            'quantity':input_quantity.value,
            'price':{
                'amount':input_sum.value
            },
            'tax': input_tax.value,
            'text': input_order_content.value
        }]
    }
    
    var formData = new FormData(form);

    xmlhttp.onreadystatechange = function() {
        if ( xmlhttp.readyState == XMLHttpRequest.DONE ) {
            if (xmlhttp.status == 200) {
                try{
                    var jsonResponse = JSON.parse(xmlhttp.responseText);
                    form.querySelector('[name="orderNumber"]').value = jsonResponse.orderNumber;
                    form.querySelector('[name="customerNumber"]').value = jsonResponse.customerNumber;
                    if( input_ym_merchant_receipt !== null ){
                        input_ym_merchant_receipt.value = JSON.stringify(objCheck);
                    }
                    form.submit();
                }
                catch(e){ alert('An error occurred while retrieving the order number: bad response!'); }
            }
            else if (xmlhttp.status == 400) {
                alert('Error: There was an error 400');
                //form.submit();
            }
            else {
               alert('Error: Something else other than 200 was returned');
               //form.submit();
            }
        }
    };

    xmlhttp.open("POST", "/bitrix/tools/kiora.yaka/numberOrder.php", true);
    xmlhttp.send(formData);
}


function isValidEmailAddress(emailAddress) {
	var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
	return pattern.test(emailAddress);
}