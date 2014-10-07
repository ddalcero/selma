<script>
$(document).ready(function(){
    $("form[name='f_Valor']").submit(function(e){
       e.preventDefault();
        dataString = $("form[name='f_Valor']").serialize();
        $.ajax({
        type: "{{ $method }}",
        url: "{{ $url }}",
        data: dataString,
        dataType: "html",
        success: function(data) {
            $("#valores").html(data)
          },
        error: function(data) {
            alert('failure: '+data);
          }
        });
    });
});
</script>
