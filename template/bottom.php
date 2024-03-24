    </center>
	<footer id="footer">
	<center>
	<span class="footerlowwidth">
        Partners:
        <table id="partners">
            <tr>
            <td><a href="http://www.confiared.com/"><img src="/images/confiared.png" title="Confiared, company of VPS in Bolivia" alt="confia red, company of VPS in Bolivia" width="283" height="36" /></a></td>
            <td><a href="https://ultracopier.herman-brule.com/"><img src="/images/ultracopier.png" title="Ultracopier is like Teracopy" alt="Ultracopier is like Teracopy" width="216" height="50" /></a></td>
            </tr>
        </table>
    </span>
	CatchChallenger<script type="text/javascript">
  document.write(" 2011-"+new Date().getFullYear());
</script>
	</center>
	</footer>

	  <div id="myModal" class="modal" style="padding:10px;">
  <div class="modal-content">
    <span class="close">&times;</span>
    <p></p>
  </div>
 </div>

<script>
var modal = document.getElementById('myModal');
var btn = document.getElementById("myBtn");
var span = document.getElementById("close");
var canClose = true;

function buy(product) {
    tempText='<form action="/buy.php" method="post"><input type="hidden" name="product" value="'+product+'">';
    tempText='<div class="modal-content" style="padding:10px;"><div class="close" id="close" onclick="document.getElementById(\'myModal\').style.display=\'none\';">&times;</div><p>';
    switch(product) {
    case 'catchchallenger':
        tempText+='You will buy for <b>15USD</b>: ';
        tempText+='<b>CatchChallenger</b> Ultimate key';
        break;
    default:
        tempText+='Error, contact: contact@herman-brule.com';
        tempText+='</p></div>';
        modal.innerHTML=tempText;
        modal.style.display = "block";
        return;
    }
    tempText+='<br />Firstly test the free version. After you can use this key to change the free version to Ultimate version.';
    tempText+='<br /><br />Email where will be send the key: <input type="email" name="email" id="email" style="border:1px solid #bbb;" />';
    tempText+=' <span id="problememail" style="color:red;display:none;"><b>Fix your email</b></span>';
    tempText+='<br />Full name: <input type="name" name="name" id="name" style="border:1px solid #bbb;" />';
    tempText+=' <span id="problemname" style="color:red;display:none;"><b>Fix your name, should be at least 7 letters, 1 first name AND 1 last name</b></span>';
    tempText+='<br /><br />Buy with:';
    
tempText+=`<div id="main">

    <div style="width:300px;height:300px;margin:10px;">
      <center>
        <h4 class="my-0 font-weight-normal"><img src="https://cdn.confiared.com/catchchallenger.herman-brule.com/images/mastercard.png" alt="" width="51px" height="31px" /> (Prefered)</h4>
      </center>
      <div style="background-color:#ddffee;">
        <center>
        Mastercard<br />
        <br />
        <button type="button" style="background-color:#008CBA;color:#fff;border: 2px solid #008CBA;padding:10px 50px;border-radius:10px;margin:0 5px;cursor: pointer;" onclick="buy2('`+product+`','cybersource')">Mastercard</button></center>
      </div>
    </div>
    
    <div style="width:300px;height:300px;margin:10px;">
      <center>
        <h4 class="my-0 font-weight-normal"><img src="https://cdn.confiared.com/catchchallenger.herman-brule.com/images/visa.png" alt="" width="94px" height="30px" /> (Prefered)</h4>
      </center>
      <div style="background-color:#ddffee;">
        <center>Visa<br />
        <br />
        <button type="button" style="background-color:#008CBA;color:#fff;border: 2px solid #008CBA;padding:10px 50px;border-radius:10px;margin:0 5px;cursor: pointer;" onclick="buy2('`+product+`','cybersource')">Visa</button></center>
      </div>
    </div>
    
    <div style="width:300px;height:300px;margin:10px;">
      <center>
        <h4 class="my-0 font-weight-normal"><img src="https://cdn.confiared.com/catchchallenger.herman-brule.com/images/paypal.png" alt="" width="128px" height="31px" /></h4>
      </center>
      <div>
        <center>Accept Mastercard<br />
        Accept Visa<br />
        <br />
        <button type="button" style="background-color: white;color: black;border: 2px solid #008CBA;padding:10px 50px;border-radius:10px;margin:0 5px;cursor: pointer;" onclick="buy2('`+product+`','paypal')">Paypal</button></center>
      </div>
    </div>
    
  </div>`;
    
    tempText+='</p></div></form>';
    modal.innerHTML=tempText;
    modal.style.display = "block";
}

function buy2(product,paiementmethod) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    var email=String(document.getElementById("email").value).toLowerCase();
    var name=String(document.getElementById("name").value);
    var emailval= re.test(email);
    var nameval= name.length>=7 && name.includes(" ");
    
    if(!emailval)
    {
        document.getElementById('email').style.backgroundColor = "#ff9999";
        document.getElementById('problememail').style.display="inline";
    }
    if(!nameval)
    {
        document.getElementById('name').style.backgroundColor = "#ff9999";
        document.getElementById('problemname').style.display="inline";
    }
    if(emailval && nameval)
    {
        document.getElementById('email').style.backgroundColor = "";
        document.getElementById('problememail').style.display="none";
        document.getElementById('close').style.display="none";
        tempText='<div class="modal-content"><p>';
        tempText+='Wait please, redirecting...';
        tempText+='</p></div></form>';
        canClose=false;
        modal.innerHTML=tempText;
        if(paiementmethod=='khipu' && window.location.href.indexOf("cybersource")>-1)
            post('/buy.php', {product: product, email: email, name: name, pay: 'cybersource', currency: 'USD', lang: 'en'});
        else
            post('/buy.php', {product: product, email: email, name: name, pay: paiementmethod, currency: 'USD', lang: 'en'});
    }
}

function post(path, params, method='post') {
  const form = document.createElement('form');
  form.method = method;
  form.action = path;

  for (const key in params) {
    if (params.hasOwnProperty(key)) {
      const hiddenField = document.createElement('input');
      hiddenField.type = 'hidden';
      hiddenField.name = key;
      hiddenField.value = params[key];

      form.appendChild(hiddenField);
    }
  }

  document.body.appendChild(form);
  form.submit();
}

window.onclick = function(event) {
    if (event.target == modal && canClose) {
        modal.style.display = "none";
    }
}
</script>

<script async src="https://www.googletagmanager.com/gtag/js?id=G-2GQ6J6V108"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-2GQ6J6V108');
</script>
	</body>
</html> 
