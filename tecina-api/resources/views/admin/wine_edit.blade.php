@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="row justify-content-center">

  <div class="col-md-8">

  <div class="card">
    <div class="card-header">
      <strong>Edición de Vino</strong>
    </div>
    <div class="card-body">
      <form action="/api/wines/{{$wine->id}}" method="POST">
        <input name="_method" type="hidden" value="PUT">
        <input name="wineName" type="text" value="{{$wine->name}}" placeholder="Nombre del vino" />
        <ul class="nav nav-tabs">
          @foreach($langs =  DB::table('languages')->get() as $lang)
            <li @if ($loop->first) class="active show" @endif><a data-toggle="tab" href="#{{$lang->code}}">{{$lang->code}}</a></li>
          @endforeach
        </ul>
        <div class="tab-content">
          @foreach($langs =  DB::table('languages')->get() as $lang)
          <div id="{{$lang->code}}" class="tab-pane fade in @if ($loop->first) active show @endif">
            <label for="description_{{$lang->code}}">
              <span>Descripción</span>
              <input type="text" id="description_{{$lang->code}}" name="description_{{$lang->code}}" value="{{@$translation[$lang->code]['description']}}"/>
            </label>
          </div>
          @endforeach
        </div>
        <input type="submit" value="Guardar cambios" />
      </form>
          <div>
            <p>Variedades</p>
            <ul id="wine_varieties">
              @foreach($varieties as $variety)
                <li id="variety_{{$variety->id}}"><span class="varietyName">{{@$varietieTranslations[$variety->id]['es']}}</span> <span class="glyphicon glyphicon-remove-circle"><a href="#" onclick="deleteWineVariety({{$wine->id}},{{$variety->id}});">Eliminar</a></span></li>
              @endforeach
            </ul>
          <label for="add_wine_variety">
            Selecciona una variedad
            <select id="add_wine_variety">
              @foreach(db::table('wine_variety_translations')->whereNotIn('id_wine_variety',array_keys($varietieTranslations))->where('id_language',1)->get() as $variety)
                <option value="{{$variety->id_wine_variety}}">
                  {{$variety->name}}
                </option>
              @endforeach
            </select>
          </label>
          <button type="button" name="button" id="add_wine_variety_button" onclick="addWineVariety({{$wine->id}})">Añadir variedad</button>
          </div>

          <img style="max-width:300px;max-height:300px;" id="wine_image" src="{{$wine->image}}" class="wine main admin" onclick="jQuery('#uploadWineImage').toggle();" />
          <div id="uploadWineImage" style="display:none">
            <input type="file" name="wineImage" id="wineImage" accept="image/x-png" placeholder="Imagen nueva" />
              <button type="button" name="wineImage" id="upload_wine_image_button" onclick="uploadWineImage({{$wine->id}})">Cambiar imagen</button>
          </div>
    </div>


</div>
</div>
</div>
</div>
<script type="text/javascript">
  function addWineVariety(wineId){
    var varietyId=$('#add_wine_variety').val();
    jQuery.ajax({
      url:'/addWineVariety/'+wineId+'/'+varietyId,
    }).done(function(data){
    if(data){
      jQuery('#wine_varieties').append('<li id="variety_'+data.varietyId+'"><span class="varietyName">'+data.varietyName+'</span><span class="glyphicon glyphicon-remove-circle"><a href="#" onclick="deleteWineVariety('+wineId+','+data.varietyId+');">Eliminar</a></span></li>');
      jQuery('#add_wine_variety>option[value="'+data.varietyId+'"]').remove();
    }
    });
  }
  // variety_7

  function deleteWineVariety(wineId,varietyId){
    console.log('Id: '+varietyId);
    console.log('#variety_'+varietyId);
    console.log(jQuery('#variety_'+varietyId));
    console.log(jQuery('#variety_'+varietyId).text());
    var name=jQuery('#variety_'+varietyId+'>span.varietyName').text();
    jQuery.ajax({
      url:'/deleteWineVariety/'+wineId+'/'+varietyId,
      data:{'varietyId':varietyId, 'wineId':wineId}
    }).done(function(data){
      console.log('El nombre de la variedad es '+name);
      if(data){
        jQuery('#variety_'+data).fadeOut();
        jQuery('#add_wine_variety').prepend('<option selected value="'+data+'">'+name+'</option>');
      }
    });
  }

  function uploadWineImage(idWine){
    var file_data = $('#wineImage').prop('files')[0];
    var form_data = new FormData();
    form_data.append('file', file_data);
    $.ajax({
          url: '/uploadWineImage/'+idWine, // point to server-side PHP script
          cache: false,
          contentType: false,
          processData: false,
          data: form_data,
          type: 'post',
          success: function(php_script_response){
              $('#wine_image').attr('src',php_script_response['img']);
              $('#uploadWineImage').fadeOut();
          },
       });
   }
</script>
@endsection
