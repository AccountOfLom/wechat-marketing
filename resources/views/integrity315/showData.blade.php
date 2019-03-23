<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>贺卡数据统计</title>
    <link rel="stylesheet" href="https://cdn.staticfile.org/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://cdn.staticfile.org/jquery/2.1.1/jquery.min.js"></script>
    <script src="https://cdn.staticfile.org/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <style>
        tbody tr:nth-of-type(odd){ background:#F8F8F8;}
        th, td {text-align: center}
    </style>
</head>
<body>
<div class="container">
    <div class="row clearfix">
        <div class="col-md-12 column">
            <h3>
                315活动数据统计
            </h3>
            <br/>
            <table class="table">
                <thead>
                <tr>
                    <th>日期</th>
                    <th>发起按钮点击次数</th>
                    <th>支持一下</th>
                    <th>排行榜按钮点击</th>
                    <th>抽奖按钮点击</th>
                    <th>修改资料</th>
                    <th>支持数</th>
                    <th>发起宣言按钮点击</th>
                    <th>发起人数</th>
                    <th>banner点击次数</th>
                    <th>抽奖次数</th>
                    <th>产生抽奖机会</th>
                    <th>派出红包金额</th>
                    <th>抽中红包数</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($data as $k => $v)
                <tr>
                    <td>{{$v['date']}}</td>
                    <td>{{$v['wyfq']}}</td>
                    <td>{{$v['zcyx']}}</td>
                    <td>{{$v['phb']}}</td>
                    <td>{{$v['cj']}}</td>
                    <td>{{$v['xgzl']}}</td>
                    <td>{{$v['zcs']}}</td>
                    <td>{{$v['fqxy']}}</td>
                    <td>{{$v['fqrs']}}</td>
                    <td>{{$v['banner']}}</td>
                    <td>{{$v['cjcs']}}</td>
                    <td>{{$v['cjjh']}}</td>
                    <td>{{$v['hbje']}}</td>
                    <td>{{$v['czhb']}}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>