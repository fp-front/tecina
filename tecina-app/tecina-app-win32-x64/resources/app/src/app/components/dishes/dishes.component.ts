import { Component, OnInit, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { TecinaApiService } from "../../services/tecina-api.service";
import { SwiperDirective, SwiperConfigInterface } from 'ngx-swiper-wrapper';


@Component({
  selector: 'app-dishes',
  templateUrl: './dishes.component.html',
  styles: []
})

export class DishesComponent implements OnInit {
  currentLang: string = 'es';
  currentFilters = {
    categories: [],
    allergens: [],
    foodTypes: []
  };
  dishes:any[] = []
  categories: any[] = [];
  foodTypes: any[] = [];
  allergens: any[] = [];
  filtersMenu = false;
  imagesPath;
  no_results: boolean = false;

  currentConfig: SwiperConfigInterface = {
    observer: true,
    a11y: true,
    direction: 'vertical',
    speed: 500,
    freeMode: true,
    freeModeSticky: true,
    slidesPerView: 2,
    navigation: {
      prevEl: '.dishes__slider__nav--prev',
      nextEl: '.dishes__slider__nav--next',
      disabledClass: 'dishes__slider__nav--disabled'
    },
  };

  translations = {
    dishes: {
      title: {
        es: 'Platos',
        en: 'Dishes',
        de: 'Geschirr'
      }
    },
    filters: {
      button: {
        es: 'Ver filtros',
        en: 'See Filters',
        de: 'Siehe Filter',
      },
      button_change: {
        es: 'Cambiar filtros',
        en: 'Change filters',
        de: 'Filter ändern',
      },
      button_clear:{
        es: 'Limpiar filtros',
        en: 'Clear filters',
        de: 'Klare Filter',
      },
      title: {
        es: 'Filtros',
        en: 'Filters',
        de: 'Filter',
      },
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
      categores_title: {
        es: 'Tipo de plato',
        en: 'Type of dish',
        de: 'Art des Gerichts',
      },
    }
  };

  title_category: {} = this.translations.dishes.title;


  @ViewChild(SwiperDirective) swiperDishes?: SwiperDirective;

  constructor(
    private _tecinaApi: TecinaApiService,
    private router: Router,
  ) {
    this._tecinaApi.getCategories().subscribe(categories => {
      this.categories = categories;
    });

    this._tecinaApi.getAllergens().subscribe(allergens => {
      this.allergens = allergens;
    });

    this._tecinaApi.getFoodTypes().subscribe(foodTypes => {
      this.foodTypes = foodTypes;
    });

    this.imagesPath = this._tecinaApi.imagesPath + "/dishes/";

    this._tecinaApi._filtersMenu.subscribe(
      filters_menu => this.filtersMenu = filters_menu
    );
  }

  initialiseState() {
    this._tecinaApi.currentFilters.flatMap(
      filters => {
        this.currentFilters = filters;

        if ((filters.categories).length == 1) {
          var i = filters.categories[0];
          this.title_category = this._tecinaApi.getObjectBy(this.categories, i, 'translate');
        } else {
          this.title_category = this.translations.dishes.title;
        }

        return this._tecinaApi.getDishes(filters).map(
          dishes => {
            return this._tecinaApi.subArray(dishes, 3);
          }
        )
      })
      .subscribe(
        dishes => {
          this.dishes = dishes;
          if(dishes.length != 0){
            this.no_results = false;
            this.goToIndex(0);
          }else{
            this.no_results = true;
          }
        });
  }

  clearFilters() {
    this._tecinaApi.setCurrentFilters({
      categories: [],
      allergens: [],
      foodTypes: []
    });
  }

  ngOnInit() {
    this._tecinaApi.currentLang.subscribe(
      resp => {
        this.currentLang = resp;
        this.initialiseState();
      }
    );
  }

  goToDish(id: number) {
    this.router.navigate(['/dish', id]);
  }

  goToIndex(i) {
    setTimeout(() => {
      this.swiperDishes.setIndex(i);
    }, 1000);
  }

  // filters 
  changeFilter(filterType: string, filterId: string, isChecked: boolean) {
    if (isChecked) {
      this.currentFilters[filterType].push(filterId);
    } else {
      let index = this.currentFilters[filterType].indexOf(filterId);

      if (index != -1) {
        this.currentFilters[filterType].splice(index, 1);
      }
    }
    this._tecinaApi.setCurrentFilters(this.currentFilters);
  }

  inArray(value, args) {
    if ((value.findIndex(element => { return element == args })) != -1) {
      return true;
    } else {
      return false;
    }
  }

  filtersMenuStatus(open) {
    this._tecinaApi.setFiltersMenu(open);
  }

  mainMenuStatus(open) {
    this._tecinaApi.setMainMenu(open);
  }

  conuntFilters() {
    var total = (this.currentFilters.categories).length + (this.currentFilters.foodTypes).length + (this.currentFilters.allergens).length;
    return total;
  }
}