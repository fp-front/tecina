import { Component, OnInit, ViewChild,QueryList,ViewChildren } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { ApiService } from "../../services/api.service";
import { SwiperDirective, SwiperConfigInterface } from 'ngx-swiper-wrapper';

@Component({
  selector: 'app-menu',
  templateUrl: './menu.component.html',
  styles: []
})
export class MenuComponent implements OnInit {

  currentLang: string = 'es';
  menu = {};
  allergens;
  pairing = true;
  imagesPath;
  menu_id;
  wines = [];
  initialSlider: number = 0;
  swiperDishesConfig: SwiperConfigInterface = {
    a11y: true,
    observer: true,
    direction: "vertical",
    speed: 500,
    freeMode: !0,
    freeModeSticky: !0,
    slidesPerView: 2,
    initialSlide:0,
    navigation: {
      prevEl: ".menu__details__slider__nav--prev",
      nextEl: ".menu__details__slider__nav--next",
      disabledClass: "menu__details__slider__nav--disabled"
    }
  };

  wineFilters = {
    varieties: [],
    do: [],
    type: []
  };

  swiperPairingConfig: SwiperConfigInterface = {
    a11y: true,
    observer: true,
    direction: "vertical",
    speed: 500,
    freeMode: !0,
    initialSlide:0,
    freeModeSticky: !0,
    slidesPerView: 3,
    navigation: {
      prevEl: ".wine-list__nav--prev",
      nextEl: ".wine-list__nav--next",
      disabledClass: "wine-list__nav--disabled"
    }
  };

  translations = {
    menu: {
      page_title: {
        es: 'Menus',
        en: 'Menus',
        de: 'Menüs',
      },
      pairing:{
        title: {
          es: 'Maridaje',
          en: 'Wine pairings',
          de: 'Wein Paarungen',
        },
        subtitle: {
          es: 'Incluida una copa del vino recomendado',
          en: 'Incl. a glass of the recommended wine',
          de: 'Inkl. ein Glas des empfohlenen Weines',
        },
        title_0: {
          es: 'Recomendación de vinos',
          en: 'Wine Recommendations',
          de: 'Weinempfehlungen',
        },
        subtitle_0: {
          es: 'No incluidos en el menú',
          en: 'Not included with the set menus',
          de: 'Nicht in den Menüs enthalten',
        },
        variety:{
          es: 'Variedad',
          en: 'Variety',
          de: 'Vielfalt',
        }
      }
    }
  };

  @ViewChildren(SwiperDirective) swiperView: QueryList<SwiperDirective>;

  constructor(
    private _activeRoute:ActivatedRoute,
    public _api: ApiService ,
    private router: Router
  ) {
    this._activeRoute.params.subscribe(
      params => {
        params.id ? this.menu_id = params.id :  this.router.navigate(['/menus']);
      }
    );

    this.imagesPath = this._api.imagesPath;

    this._api._pairing.subscribe(
      pairing => this.pairing = pairing 
    );

  }

  initialiseState() {
    this._api.getMenus().map(
      menus => {
        let menu = this._api.getObjectBy(menus, this.menu_id);
        if(('menu_wines' in menu)){
          this.wines = this._api.subArray( (menu['menu_wines']).slice(0) ,2 );          
        }
        return menu;
      }
    ).subscribe(
      menu => {
        this.menu = menu;
        
        if(this.menu == []){
          this.router.navigate(['/menus'])
        }
        this.goToIndexDishes(this.initialSlider);
        this.goToIndexPairing(this.initialSlider);        
      });
  }

  ngOnInit() {
    this._api.currentLang.subscribe(
      resp => {
        this.currentLang = resp;
        this.initialiseState();
      });
  }

  goToIndexDishes(i,delay=1000) {
    setTimeout(() => {
      (this.swiperView['_results'][0]).setIndex(i);
    }, delay);
  }

  hasProp(o, name) {
    return o.hasOwnProperty(name);
  }

  goToIndexPairing(i,delay=1000) {
    setTimeout(() => {
    (this.swiperView['_results'][1]).setIndex(i);
    }, delay);
  }

  pairingStatus(open) {
    this._api.setPairing(open);
  }


}
