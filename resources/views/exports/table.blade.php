<?php
	/**
	 * Created by PhpStorm.
	 * User: shaun
	 * Date: 2020/8/3
	 * Time: 15:51
	 */
	?>
    <table>
    <thead>
    <tr>
        <th>姓名</th>
        <th>案件名稱</th>
        <th>財報月份</th>
        <th>毛利</th>
        <th>錯誤獎金%</th>
        <th>正確獎金%</th>
        <th>錯誤獎金</th>
        <th>正確獎金</th>
    </tr>
    </thead>
    <tbody>
    @foreach($datas as $userName => $items)
        @foreach($items as $item)
            <tr>
                <td>{{ $userName }}</td>
                <td>{{ $item['campaign_name'] }}</td>
                <td>{{ $item['set_date'] }}</td>
                <td>{{ $item['profit'] }}</td>
                <td>{{ $item['error_bonus_rate'] }}</td>
                <td>{{ $item['real_bonus_rate'] }}</td>
                <td>{{ $item['error_provide'] }}</td>
                <td>{{ $item['real_provide'] }}</td>
            </tr>
        @endforeach
    @endforeach
    </tbody>
    </table>
