function doClick() {
  var datas = ['one', 'two', 'three'];
  var jsonDatas = JSON.stringify(datas);
  new GAjax().send('index.php/index/model/demo/send', 'datas=' + jsonDatas, function (xhr) {
    alert(xhr.responseText);
  });
}
