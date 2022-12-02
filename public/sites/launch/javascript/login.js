<script type="text/javascript">
   function launchLogin(){
        let password = document.getElementById('password').value;
        password= hashCode(password);
        if (password == -1594813416){
            localStorage.setItem("launch-teaching-notes", "show");
            document.getElementById('response').innerHTML ="You can now show teacher notes on each page of the Training.";
        }
        else{
            document.getElementById('response').innerHTML ="I am sorry, but that is not the right password. Check to make sure you capitalize the right letters.";
        }
   }

   function hashCode(s) {
    for(var i = 0,  h = 0; i < s.length; i++)
        h = Math.imul(31, h) + s.charCodeAt(i) | 0;
    return h;
   }
</script>