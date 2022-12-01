'use strict';

const toggleHidden = (...fields) => {

  fields.forEach((field) => {

    if (field.hidden === true) {

      field.hidden = false;

    } else {

      field.hidden = true;

    }
  });
};

const labelHidden = (form) => {

  form.addEventListener('focusout', (evt) => {

    const field = evt.target;
    const label = field.nextElementSibling;

    if (field.tagName === 'INPUT' && field.value && label) {

      label.hidden = true;

    } else if (label) {

      label.hidden = false;

    }
  });
};

const toggleDelivery = (elem) => {

  const delivery = elem.querySelector('.js-radio');
  const deliveryYes = elem.querySelector('.shop-page__delivery--yes');
  const deliveryNo = elem.querySelector('.shop-page__delivery--no');
  const fields = deliveryYes.querySelectorAll('.custom-form__input');

  delivery.addEventListener('change', (evt) => {

    if (evt.target.id === 'dev-no') {

      fields.forEach(inp => {
        if (inp.required === true) {
          inp.required = false;
        }
      });


      toggleHidden(deliveryYes, deliveryNo);

      deliveryNo.classList.add('fade');
      setTimeout(() => {
        deliveryNo.classList.remove('fade');
      }, 1000);

    } else {

      fields.forEach(inp => {
        if (inp.required === false) {
          inp.required = true;
        }
      });

      toggleHidden(deliveryYes, deliveryNo);

      deliveryYes.classList.add('fade');
      setTimeout(() => {
        deliveryYes.classList.remove('fade');
      }, 1000);
    }
  });
};

const filterWrapper = document.querySelector('.filter__list');
if (filterWrapper) {

  filterWrapper.addEventListener('click', evt => {

    const filterList = filterWrapper.querySelectorAll('.filter__list-item');

    filterList.forEach(filter => {

      if (filter.classList.contains('active')) {

        filter.classList.remove('active');

      }

    });

    const filter = evt.target;

    filter.classList.add('active');

  });

}

const shopList = document.querySelector('.shop__list');

//* ф-я асинхронной отправки данных, используется в коде ниже
async function sendData(data, url) {
  return await fetch(url, {
    method: 'POST',
    body: data,
  });
}
//*
if (shopList) {

  shopList.addEventListener('click', (evt) => {

    const prod = evt.path || (evt.composedPath && evt.composedPath());;

    if (prod.some(pathItem => pathItem.classList && pathItem.classList.contains('shop__item'))) {
      //*получаем id товара
      const target = prod[0].firstElementChild;
      const productId = target.textContent;
      // alert(productId);
      //*
      const shopOrder = document.querySelector('.shop-page__order');

      toggleHidden(document.querySelector('.intro'), document.querySelector('.shop'), shopOrder);

      window.scroll(0, 0);

      shopOrder.classList.add('fade');
      setTimeout(() => shopOrder.classList.remove('fade'), 1000);

      const form = shopOrder.querySelector('.custom-form');
      labelHidden(form);

      toggleDelivery(shopOrder);

      const buttonOrder = shopOrder.querySelector('.button');
      const popupEnd = document.querySelector('.shop-page__popup-end');
      //*помещаем id товара в соответствующее поле формы заказа
      const formOrder = shopOrder.querySelector('.js-order');
      const fieldToId = formOrder.querySelector('.js-product-id');
      fieldToId.setAttribute('value', productId);
      //*
      buttonOrder.addEventListener('click', (evt) => {
        form.noValidate = true;

        const inputs = Array.from(shopOrder.querySelectorAll('[required]'));

        inputs.forEach(inp => {

          if (!!inp.value) {

            if (inp.classList.contains('custom-form__input--error')) {
              inp.classList.remove('custom-form__input--error');
            }

          } else {

            inp.classList.add('custom-form__input--error');

          }
        });
        //блок после валидации формы
        if (inputs.every(inp => !!inp.value)) {

          evt.preventDefault();

          toggleHidden(shopOrder, popupEnd);

          popupEnd.classList.add('fade');

          setTimeout(() => popupEnd.classList.remove('fade'), 1000);

          //*
          async function handleFormSubmit(event) {
            event.preventDefault();
            // console.log(event.target.parentNode);
            //здесь формируется массив данных для передачи
            const data = new FormData(event.target.parentNode);
            const url = '/src/order_handler.php';

            const { status, statusText, error } = await sendData(data, url);
            
            if (status === 200) {
              console.log('Order: ' + statusText);
            } else {
              alert(error.message);
            }
          }

          handleFormSubmit(evt);
          //*

          window.scroll(0, 0);

          const buttonEnd = popupEnd.querySelector('.button');

          buttonEnd.addEventListener('click', (evt) => {

            popupEnd.classList.add('fade-reverse');

            setTimeout(() => {

              popupEnd.classList.remove('fade-reverse');

              toggleHidden(popupEnd, document.querySelector('.intro'), document.querySelector('.shop'));

            }, 1000);

          });

        } else {
          window.scroll(0, 0);
          evt.preventDefault();
        }
      });
    }
  });
}

const pageOrderList = document.querySelector('.page-order__list');
if (pageOrderList) {

  pageOrderList.addEventListener('click', evt => {

    if (evt.target.classList && evt.target.classList.contains('order-item__toggle')) {
      var path = evt.path || (evt.composedPath && evt.composedPath());
      Array.from(path).forEach(element => {

        if (element.classList && element.classList.contains('page-order__item')) {

          element.classList.toggle('order-item--active');

        }

      });

      evt.target.classList.toggle('order-item__toggle--active');

    }

    if (evt.target.classList && evt.target.classList.contains('order-item__btn')) {

      const status = evt.target.previousElementSibling;

      if (status.classList && status.classList.contains('order-item__info--no')) {
        status.textContent = 'Выполнено';
      } else {
        status.textContent = 'Не выполнено';
      }

      status.classList.toggle('order-item__info--no');
      status.classList.toggle('order-item__info--yes');

    }

  });

}

const checkList = (list, btn) => {

  if (list.children.length === 1) {

    btn.hidden = false;

  } else {
    btn.hidden = true;
  }

};
const addList = document.querySelector('.add-list');
if (addList) {

  const form = document.querySelector('.custom-form');
  labelHidden(form);

  const addButton = addList.querySelector('.add-list__item--add');
  const addInput = addList.querySelector('#product-photo');

  checkList(addList, addButton);

  addInput.addEventListener('change', evt => {

    const template = document.createElement('LI');
    const img = document.createElement('IMG');

    template.className = 'add-list__item add-list__item--active';
    template.addEventListener('click', evt => {
      addList.removeChild(evt.target);
      addInput.value = '';
      checkList(addList, addButton);
    });

    const file = evt.target.files[0];
    const reader = new FileReader();

    reader.onload = (evt) => {
      img.src = evt.target.result;
      template.appendChild(img);
      addList.appendChild(template);
      checkList(addList, addButton);
    };

    reader.readAsDataURL(file);

  });
  
  const button = document.querySelector('.button');
  const popupEnd = document.querySelector('.page-add__popup-end');
 //* 
  const addProductForm = document.querySelector('.js-add-product');

  addProductForm.addEventListener('submit', (ev) => {
    ev.preventDefault();

    // alert('Отправка!');
    form.hidden = true;
    popupEnd.hidden = false;
   
    addProductForm.submit();
  });
  //*

  // button.addEventListener('click', (evt) => {
  //
  //   evt.preventDefault();
  //
  //   form.hidden = true;
  //   popupEnd.hidden = false;
  //
  // })

}

const productsList = document.querySelector('.page-products__list');
if (productsList) {

  productsList.addEventListener('click', evt => {

    const target = evt.target;

    if (target.classList && target.classList.contains('product-item__delete')) {

      productsList.removeChild(target.parentElement);

    }

  });

}

// jquery range maxmin
if (document.querySelector('.shop-page')) {
  //*
  let minPriceStr = document.querySelector('.min-price').textContent.replace(/\s/g, '');
  let maxPriceStr = document.querySelector('.max-price').textContent.replace(/\s/g, '');
  let minPrice = parseInt(+/\d+/.exec(minPriceStr));
  let maxPrice = parseInt(+/\d+/.exec(maxPriceStr));
  // console.log(minPrice);
  // console.log(maxPrice);
  //*
  $('.range__line').slider({
    min: 350,
    max: 32000,
    // values: [350, 32000],
    values: [minPrice, maxPrice],
    range: true,
    stop: function(event, ui) {

      $('.min-price').text($('.range__line').slider('values', 0) + ' руб.');
      $('.max-price').text($('.range__line').slider('values', 1) + ' руб.');

    },
    slide: function(event, ui) {

      $('.min-price').text($('.range__line').slider('values', 0) + ' руб.');
      $('.max-price').text($('.range__line').slider('values', 1) + ' руб.');

    }
  });

}

//*
//обработка формы фильтра товаров
const filterFormEl = document.querySelector('.js-filter-form');
//ф-я обрезает из строки с ценой часть с обозначением валюты, удаляет из неё пробелы и возвращает результат в виде числа
const cutString = (str, pattern) => {
  let result = '';

  if (str.includes(pattern)) {
    const index = str.indexOf(pattern);
    str = str.slice(0, index);
  }

  result = Number(str.replace(/\s/g, ''));

  return result;
}
//ф-я собирает данные из формы
const getFilterData = () => {
  const { elements } = filterFormEl;
  const data = new FormData();

  Array.from(elements)
    .filter((item) => !!item.name)
    .forEach((element) => {
      const { name, type } = element;
      const value = type === 'checkbox' ? element.checked : element.value;

      data.append(name, value);
    });
  //данные из jquery-range в поля формы не подставляются, будем забирать их из соответствующих текстовых полей
  const minPrice = filterFormEl.querySelector('.min-price').textContent;
  const maxPrice = filterFormEl.querySelector('.max-price').textContent;
  //паттерн для валюты в которой выводится цена
  const pattern = 'руб';
  //добавляем в форму данные из полей с ценой
  data.append('filter-min-price', cutString(minPrice, pattern));
  data.append('filter-max-price', cutString(maxPrice, pattern));
  //собираем массив ссылок представленных внутри поля формы
  const filterFormLinksArr = filterFormEl.querySelectorAll('.js-form-link');
  //проходим по ссылкам, выбираем из них активную и соответствующие данные добавляем в форму
  for (let i = 0; i < filterFormLinksArr.length; i++) {
    // console.log(filterFormLinksArr[i].getAttribute('href'));
    if (filterFormLinksArr[i].classList.contains('active')) {
      const hrefParts = filterFormLinksArr[i].getAttribute('href').split('=');
      
      data.append('filter-products', hrefParts[1]);
    }
  }
  // console.log(Array.from(data.entries()));
  return data;
}

//ф-я формирует get-строку для запроса
const makeRequestStr = (data) => {
  //массив данных полученный из формы
  const arrFromForm = Array.from(data);
  // console.log(arrFromForm);
  //получаем начальную часть текущего URL
  let url = window.location.origin;
  //iформируем строку запроса
  let requestStr = `${url}/?`;

  for (let i = 0; i < arrFromForm.length; i++) {
    let elem = arrFromForm[i].join('=');
    
    requestStr = `${requestStr}&${elem}`;
  }
  // console.log(requestStr);
  return requestStr;
}
//проверка присутствия на странице нужного элемента нужна т.к. 
//один js-скрипт привязан ко всем страницам проекта
if (filterFormEl) {
  filterFormEl.addEventListener('submit', (ev) => {
    ev.preventDefault();

    const formData = getFilterData();
    const url = makeRequestStr(formData);

    window.location.replace(url);
  });
}
//*
