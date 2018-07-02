<?php

namespace App\Http\Controllers;

use App\Menu;
use App\Dish;
use Illuminate\Http\Request;
use DB;
use Intervention\Image\ImageManager;
// use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		$data = [];
		foreach(Menu::all() as $menu)
		{
			$menu->translate = prettyTranslate($menu->getTranslate()->get());
			$menu->dishes = $menu->dishes()->get()->pluck('id')->toArray();
			$menu->wines = $menu->wines()->get()->pluck('id')->toArray();
			$data[] = $menu;
		}

        return response()->json($data,200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        return response()->json(['probando'=>$request],200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      $menu=Menu::find($id);
      $menu->translate = prettyTranslate($menu->getTranslate()->get());
      foreach($menu->dishes()->get() as $dishId ){
        $dish=prettyTranslate(Dish::find($dishId->id)->getTranslate()->get())['es'];
        // dd(prettyTranslate($dish->getTranslate()->get()));
        // dd($dish);
        $menu->dishes[] = prettyTranslate(Dish::find($dishId->id)->getTranslate()->get())['es'];
      }
      $menu->wines = $menu->wines()->get()->pluck('id')->toArray();
      dd($menu->toArray());
      return response()->json($menu,200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $menu=Menu::find($id);
        $dishes=$menu->dishes()->get();

        $my_dishes=[];
        foreach($dishes as $dish){
          $translation=prettyTranslate($dish->getTranslate()->get());
          $my_dishes[$dish->id]=$translation['es'];
        }
        $wines=$menu->wines()->get();

        $my_wines=[];
        foreach($wines as $wine){
          // $translation=prettyTranslate($wine->getTranslate()->get());
          $my_wines[$wine->id]=$wine['name'];
        }
        $values=[
          'id'=>$menu->id,
          'image'=>$menu->image,
          'translation'=>prettyTranslate($menu->getTranslate()->get()),
          'dishes'=>$my_dishes,
          'wines'=>$my_wines
        ];
        return view('admin.menu_edit', $values);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
          $respuesta=[];
          $menu = \App\Menu::find($id);
          $langs=\App\Language::all();
          $translates = prettyTranslate($menu->getTranslate()->get());
          foreach($langs as $lang){
            $translate=$translates[$lang->code];
            $respuesta[$lang->code]=$translate;
            DB::table('menus_translations')->where('id_menu',$id)->where('id_language',$lang->id)->update(['name'=>$request['name_'.$lang->code],'description'=>$request['description_'.$lang->code]]);
            // $lang_id=DB::table->where('code',$lang)->first()->id;
            // $respuesta[$lang_id]=$translate;
          //  $menu->translates[$lang]['name']=>$request['name_'.$lang];
          //  $menu->translates[$lang]['description']=>$request['description_'.$lang];
          }
          // return response()->json($respuesta,200);
          // return response()->json($menu,200);
          return redirect('api/menus/'.$id.'/edit');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function deleteDishMenu($dishId, $menuId)
    {
      $data=false;
      if(DB::table('dishes_menus')->where('id_dish',$dishId)->where('id_menu',$menuId)->delete()){
        $data=$dishId;
      }
      return response()->json($data,200);
    }

    public function addDishMenu($dishId, $menuId)
    {
      $data=false;
      if(DB::table('dishes_menus')->insert(['id_dish'=>$dishId,'id_menu'=>$menuId])){
        $dishName= DB::table('dishes_translations')->where('id_dish',$dishId)->where('id_language',1)->first()->name;
        $data=['dishId'=>$dishId,'dishName'=>$dishName];
      }
      return response()->json($data,200);
    }

    public function deleteWineMenu($wineId, $menuId)
    {
      $data=false;
      if(DB::table('menus_wines')->where('id_wine',$wineId)->where('id_menu',$menuId)->delete()){
        $data=$wineId;
      }
      return response()->json($data,200);
    }

    public function addWineMenu($wineId, $menuId)
    {
      $data=false;
      if(DB::table('menus_wines')->insert(['id_wine'=>$wineId,'id_menu'=>$menuId])){
        $wineName=\App\Wine::find($wineId)->name;
        $data=['wineId'=>$wineId,'wineName'=>$wineName];
      }
      return response()->json($data,200);
    }

    public function uploadMenuImage(Request $request, $menuId){
      $respuesta=['menuId'=>$menuId];
      if ($request->hasFile('file'))
      {
      $file = $request->file('file');
      $image_name = time()."-".$file->getClientOriginalName();
      $img_route='/img/menus/'. $image_name;
      $file->move('img/menus', $image_name);
      // $image = Image::make(sprintf('uploads/%s', $image_name))->resize(1760, 960)->save();
      // $image = Image::make(sprintf('img/%s', $image_name))->resize(1760, 960)->save();// $image = new Image('img/'. $image_name);
      $menu = \App\Menu::find($menuId);
      $menu->image=$img_route;
      $menu->save();
      $respuesta ['img'] = $img_route;
      }else{
        $respuesta ['img'] = 'not-found.jpg';
      }
      // hay que redimensionarla a este tamaño: 1760 × 960
      return response()->json($respuesta,200);
      // dd($request);
    }

}
