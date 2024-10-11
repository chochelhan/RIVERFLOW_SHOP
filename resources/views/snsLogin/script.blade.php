<script>
var debug = false;
var data = <?php echo json_encode($data); ?>;
if(debug) {
    window.addEventListener('message', (event) => {
        event.source.postMessage(data,event.origin);
        window.close();
    }, false);
} else {
   window.opener.snsLoginResult(data);
   window.close();
}

</script>