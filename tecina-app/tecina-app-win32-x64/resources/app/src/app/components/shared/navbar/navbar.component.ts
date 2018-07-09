import { Component, OnInit,OnDestroy } from '@angular/core';
import { TecinaApiService } from "../../../services/tecina-api.service";
import { ActivatedRoute, Router } from '@angular/router';


@Component({
  selector: 'app-navbar',
  templateUrl: './navbar.component.html',
  styles: []
})

export class NavbarComponent implements OnInit, OnDestroy {
  currentLang = 'es';
  langs:any[] = [];
  categories:any[] = [];
  foodTypes:any[] = [];
  allergens:any[] = [];
  dishes:any[] = [];
  menus:any[] = [];
  wines:any[] = [];
  drinks:any[] = [];
  wineHighlight;
  imagesPath:string;
  mainMenu:boolean = false;
  translations = {
   nav: {
     allergen_title: {
       es: 'Tipo de alérgeno',
       en: 'Type of allegry',
       de: 'Art der Allergie',
     },
     foodtypes_title: {
       es: 'Tipo de comida',
       en: 'Type of food',
       de: 'Art von Essen',
     },
     wine_title: {
       es: 'Carta de Vinos',
       en: 'Wine menu',
       de: 'Weinkarte',
     },
     drinks_title: {
      es: 'Carta de Bebidas',
      en: 'Drinks menu',
      de: 'Getränkekarte',
    },
    variety:{
      es: 'Variedad',
      en: 'Variety',
      de: 'Vielfalt',
    },
    do:{
      es: 'D. O.',
      en: 'D. O.',
      de: 'D. O.',
    },
    menu_title: {
       es: 'Menus',
       en: 'Menus',
       de: 'Menüs',
     },
    filter_title: {
       es: 'Filtros',
       en: 'Filters',
       de: 'Filter',
     },
    menu: {
       es: 'Ver Carta',
       en: 'SEE MENU',
       de: 'SIEHE MENÜ',
     },
     button:{
      es: 'Limpiar filtros',
      en: 'Clear filters',
      de: 'Klare Filter',
    }
   }
  };

  currentFilters = {
    categories: [],
    allergens: [],
    foodTypes: []
  };

  constructor( 
      public _tecinaApi: TecinaApiService ,
      private router: Router,
    ) {    
      this.imagesPath = this._tecinaApi.imagesPath + "/wines/";
                                       
      this._tecinaApi.getLanguages().subscribe(languages => {
        this.langs = languages;
      });

      this._tecinaApi.getCategories().subscribe(categories => {
        this.categories = categories;
      });
  
      this._tecinaApi.getFoodTypes().subscribe(foodTypes => {
        this.foodTypes = foodTypes;
      });
  
      this._tecinaApi.getAllergens().subscribe( allergens => {
        this.allergens = allergens;
      });

      this._tecinaApi.getDrinks().subscribe( drinks => {
        this.drinks = drinks;
      });

      this._tecinaApi.getMenus().subscribe(menus => {
        this.menus = menus;
      });

      // DO cambiar por el vino destacado
      this._tecinaApi.getWines().flatMap(wines => {
        this.wines = wines;
        return this._tecinaApi.getHighLightedWine();
      }).subscribe(
        wineHighlight => {
          if((this.wines).length > 0 ){
            this.wineHighlight = this._tecinaApi.getObjectBy(this.wines,wineHighlight);
          }
      });

      this._tecinaApi._mainMenu.subscribe(
        main_menu => this.mainMenu = main_menu
      );
  }

  initialiseInvites() {
    this._tecinaApi.currentFilters.flatMap( 
      filters => {
        this.currentFilters = filters;
        return this._tecinaApi.getDishes( filters , [] )
    })
    .subscribe(
      dishes => { 
        this.dishes = dishes;
    });
  }

  getFilteredDishes ( _categories ){
    return this._tecinaApi.filterAll( this.dishes , this.currentFilters ,_categories);
  }

  changeFilter( filterType:string , filterId:string, isChecked: boolean) {
    if(isChecked) {
      this.currentFilters[filterType].push(filterId);
    } else {
      let index = this.currentFilters[filterType].indexOf(filterId);

      if(index != -1) {
        this.currentFilters[filterType].splice(index, 1);
      }
    }
    this._tecinaApi.setCurrentFilters( this.currentFilters );
  }
 
  clearFilters(){
    this._tecinaApi.setCurrentFilters( {
      categories: [],
      allergens: [],
      foodTypes: []
    });
  }

  ngOnInit(){
    this._tecinaApi.currentLang.subscribe(
      resp => {
        this.currentLang = resp;
        this.initialiseInvites();
      }
    );
  }
  
  changeLang( new_lang ){
    this._tecinaApi.setCurrentLAng( new_lang );
  }

  goToDishes( categoryId:number ){
    this.currentFilters.categories = [categoryId];
    this._tecinaApi.setCurrentFilters( this.currentFilters );
    this.router.navigate(['/dishes']);
  }

  filtersMenuStatus( open ){
    this._tecinaApi.setFiltersMenu(open);
  }

  mainMenuStatus( open ){
    this._tecinaApi.setMainMenu(open);
  }

  inArray( value , args ){
    if ((value.findIndex(element => { return element == args })) != -1){
      return true;
    }else{
      return false;
    }
  }

  ngOnDestroy() {}

}