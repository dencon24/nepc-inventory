const hamBurger = document.querySelector(".toggle-btn");

hamBurger.addEventListener("click", function () {
  document.querySelector("#sidebar").classList.toggle("expand");
});

$(document).ready(function () {
  $('#link-hardware').click(function (event) {
    event.preventDefault()
    $('#main-container').load('hardware-table.html #example', function () {
      new DataTable('#example')
    })
  })

  $('#link-software').click(function (event) {
    event.preventDefault()
    $('#main-container').load('software-table.html #example', function () {
        new DataTable('#example')
    })
  })

  $('#link-contracts').click(function (event) {
    event.preventDefault()
    $('#main-container').load('contracts-table.html #example', function () {
        new DataTable('#example')
    })
  })
})
