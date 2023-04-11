@extends('user/layouts/parts/master')
@section('content')
<!-- banner -->
<div class="">
    @include('user/layouts/bannerSlider/bannerSlider')
</div>

<div class="emi">
  <div class="container" style="margin-top: 2%;">
    <div class="row">
      <div class="col-10 card">
        <div class="col-sm-7">
          <H1>EMI Calculator</H1>
          <form name="loan-form">
            <div class='loanBlock'>
              <h3>Loan Amount</h3>
              <input type=number id="amount" style="border-radius:5px"><br>
            </div>
            <div class='aprBlock'>
              <h3>Interest Rate (in %)</h3>
              <input type="number" name="apr" id="apr" style="border-radius:5px"><br>
            </div>
            <div class='tenureBlock'>
              <h3>Tenure (yrs)</h3>
              <input type=number name="tenure" id="tenure" style="border-radius:5px"><br>
              <br>
            </div>
            <div style="display: flex">
              <button style="border-radius:2px;padding: 8px 30px!important; margin-top: 20px;" id="find" class="btn-sub read-more-btn btn-lg btn btn-primary">Find Emi</button>
            </div>
          </form>
        </div>
        <div class="col-sm-5 emi-result">

          <h2>Results</h2><br />
          <h3>Total EMI:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span id="emi"></span></h3>
          <h3>Total Amount: &nbsp;&nbsp;&nbsp;&nbsp;<span id="totalAmt"></span></h3>
          <h3>Total Interest:&nbsp;&nbsp;&nbsp;&nbsp;<span id="totalInt"></span></h3>
          <div id="chartContainer" style="height: 300px; width: 100%;"></div>

        </div>
      </div>
      
    </div>
  </div>
</div>


@endsection('content')

<style>
  .emi {
    /* display: grid; */
    margin-bottom: 25px;
    /* align-items: center;
    justify-content: center;
    align-content: center; */
    /* background-color: #FFBDA2; */
  }
  input {
    width: 100%;
    height: 40px;
    color: black;
    padding: 10px 20px;
  }
  /* .emi .card{
    display: flex;
    flex-direction: row;
  } */
  .emi .container .card{
    display: flex;
    width: 100%;
    margin: auto;
  }
  .emi-result{
    border-left: 1px solid #eeeeee;
  }
  @media (max-width: 768px){
    .emi .container .card{
      flex-direction: column;  
    }  
    .emi-result{
      border-left: none;
    }
  }

  .card {
    margin: 0 auto;
    border: 1px solid #eeeeee;
    border-radius: 10px;
    padding: 10px 25px 30px;
    box-shadow: rgb(100 100 111 / 20%) 0px 7px 29px 0px;
    /* background-color: #0C0D29;
    box-shadow: -10px 0px 0px 0px #F0A384;
    padding: 20px;
    color: white; */
  }
  .emi .card form{
    width: 400px;
    max-width: 100%;
    margin: 50px auto;
  }

  h1 {
    text-align: center;
  }

  .option {
    display: flex;
    justify-content: space-between;
    margin-top: 5px;
  }

  .password-result {
    display: flex;
    margin-bottom: 20px;
  }

  .btn-sub {
    background-color: #181934;
    color: #fff;
    border: 0;
    padding: 10px 30px;
    /* letter-spacing: 3px; */
    text-transform: uppercase;
    margin-top: 10px;
    border: 1px solid white;
    display: flex;
    margin: auto;
    font-weight: bolder;

  }
  .btn-sub:hover{
    box-shadow: rgba(149, 157, 165, 0.2) 0px 8px 24px;
  }

  .button {
    display: flex;
    justify-content: center;
    padding: 10px 20px;
    font-weight: bolder;
  }

  @media only screen and (min-width: 768px) {
    .card {
      width: 100%;
    }

  }
</style>