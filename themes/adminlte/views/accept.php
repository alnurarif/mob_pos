<script type="text/javascript" src="https://jstest.authorize.net/v1/Accept.js" charset="utf-8"></script>
<script type="text/javascript">
function sendPaymentDataToAnet() {
    var secureData = {}; authData = {}; cardData = {};

    // Extract the card number, expiration date, and card code.
    cardData.cardNumber = document.getElementById('cardNumberID').value;
    cardData.month = document.getElementById('monthID').value;
    cardData.year = document.getElementById('yearID').value;
    cardData.cardCode = document.getElementById('cardCodeID').value;
    secureData.cardData = cardData;

    authData.clientKey = "46NXq3zyKU932CNmjjafv3t2ED9f3A3r6qB3N7gekU8apz94q2jYV7X3r6KW9wx9";
    authData.apiLoginID = "9nuC5H37";
    secureData.authData = authData;

    // Pass the card number and expiration date to Accept.js for submission to Authorize.Net.
    Accept.dispatchData(secureData, responseHandler);

    // Process the response from Authorize.Net to retrieve the two elements of the payment nonce.
    // If the data looks correct, record the OpaqueData to the console and call the transaction processing function.
    function responseHandler(response) {
        if (response.messages.resultCode === "Error") {
            for (var i = 0; i < response.messages.message.length; i++) {
                console.log(response.messages.message[i].code + ": " + response.messages.message[i].text);
            }
            alert("acceptJS library error!")
        } else {
/*
            console.log(response.opaqueData.dataDescriptor);
            console.log(response.opaqueData.dataValue);
*/
            processTransaction(response.opaqueData);
        }
    }
    
    
}

function processTransaction(responseData) {
    
    var transactionForm = document.getElementById("form_aaa");
  
    amount = document.createElement("input")
    amount.hidden = true;
    amount.value = document.getElementById('amount').value;
    amount.name = "amount";
    transactionForm.appendChild(amount);

    dataDesc = document.createElement("input")
    dataDesc.hidden = true;
    dataDesc.value = responseData.dataDescriptor;
    dataDesc.name = "dataDesc";
    transactionForm.appendChild(dataDesc);

    dataValue = document.createElement("input")
    dataValue.hidden = true;
    dataValue.value = responseData.dataValue;
    dataValue.name = "dataValue";
    transactionForm.appendChild(dataValue);

    //submit the new form
    transactionForm.submit();
}
</script>
<?php echo form_open($this->uri->uri_string(), 'id="form_aaa"'); ?>
    <?php echo lang('Card Number');?><br>
    <input type="tel" id="cardNumberID" value="5424000000000015" autocomplete="off" /><br><br>
    <?php echo lang('Expiration Date (Month)');?><br>
    <input type="text" id="monthID" value="12" value="12" /><br><br>
    <?php echo lang('Expiration Date (Year)');?><br>
    <input type="text" id="yearID" value="2025" value="2025" /><br><br>
    <?php echo lang('Card Security Code');?><br>
    <input type="text" id="cardCodeID" value="123" /><br><br>
    <?php echo lang('Amount');?><br>
    <input type="text" id="amount" value="10.00" /><br><br>
<?php echo form_close(); ?>
<button type="button" id="submitButton" onclick="sendPaymentDataToAnet()">Pay</button>

