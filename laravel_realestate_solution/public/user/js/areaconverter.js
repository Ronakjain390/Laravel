new Vue({
    data(){
      return{
        acre: 0.0,
        square_mile:0.0,
        square_foot: 0.0,
        square_inch: 0.0,
        square_km: 0.0,
        square_meter: 0.0,
        hectare: 0.0,
        square_yard: 0.0
      }
    },
    methods:{
      area(area, value) {
        
        var vm = this
        
        if (area == "acre") {
          vm.hectare = value * 0.404686;
          vm.square_foot = value * 43560;
          vm.square_inch = value * 6.273e6;
          vm.square_km = value * 0.00404686;
          vm.square_meter = value * 4046.86;
          vm.square_mile = value * 0.0015625;
          vm.square_yard = value * 4840;
        } else if (area == "square_mile") {
          vm.acre = value * 640; 
          vm.square_foot = value * 2.788e7;
          vm.square_inch = value * 4.014e9;
          vm.square_km = value * 2.58999;
          vm.square_meter = value * 2.59e6;
          vm.hectare = value * 258.999;
          vm.square_yard = value * 3.098e6;
        } else if (area == "square_foot") {
          vm.acre = value * 2.2957e-5;
          vm.hectare = value * 9.2903e-6;
          vm.square_inch = value * 144;
          vm.square_km = value * 9.2903e-8;
          vm.square_meter = value * 0.092903;
          vm.square_mile = value * 3.587e-8;
          vm.square_yard = value * 0.111111;
        } else if (area == "square_inch") {
          vm.acre = value * 1.5942e-7;
          vm.square_foot = value * 0.00694444;
          vm.hectare = value * 6.4516e-8;
          vm.square_km = value * 6.4516e-10;
          vm.square_meter = value * 0.00064516;
          vm.square_mile = value * 2.491e-10;
          vm.square_yard = value * 0.000771605;
        } else if (area == "square_km") {
          vm.acre = value * 247.105;
          vm.square_foot = value * 1.076e7;
          vm.square_inch = value * 1.55e9;
          vm.hectare = value * 100;
          vm.square_meter = value * 1e6;
          vm.square_mile = value * 0.386102;
          vm.square_yard = value * 1.196e6;
        } else if (area == "square_meter") {
          vm.acre = value * 0.000247105;
          vm.square_foot = value * 10.7639;
          vm.square_inch = value * 1550;
          vm.square_km = value * 1e-6;
          vm.hectare = value * 1e-4;
          vm.square_mile = value * 3.861e-7;
          vm.square_yard = value * 1.19599;
        } else if (area == "hectare") {
          vm.acre = value * 2.47105;
          vm.square_foot = value * 107639;
          vm.square_inch = value * 1.55e7;
          vm.square_km = value * 0.01;
          vm.square_meter = value * 10000;
          vm.square_mile = value * 0.00386102;
          vm.square_yard = value * 11959.9;
        } else if (area == "square_yard") {
          vm.acre = value * 0.000206612;
          vm.square_foot = value * 9;
          vm.square_inch = value * 1296;
          vm.square_km = value * 8.3613e-7;
          vm.square_meter = value * 0.836127;
          vm.square_mile = value * 3.2283e-7;
          vm.hectare = value * 8.3613e-5;
        }
      }
      
    }
  }).$mount('#app')