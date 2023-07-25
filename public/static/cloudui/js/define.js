/**
 * 二开自定义js文件
 */
/**
 *description :export things  included ('orders and others')
 * @param index
 * @param urlParams the params of submit to the server
 */
function exportItem(index, urlParams) {
    //more items means server action
    var itemArr = [
        'exportOrder', '/index/balance/exportBalanceCash', '/exportUserCal', '/admin/Balance/exportBalanceCash',
        '/admin/Balance/exportBalance', '/index/Balance/exportBalanceChange', 'exportDfOrder', 'exportMsBills','/admin/Balance/exportBalanceChange',
        '/admin/Orders/exportUserCal','/admin/Ms/exportMsList','/admin/daifu_Orders/exportDaifuStats'
    ]

    locationUrl = itemArr[index];
    window.location.href = locationUrl + '?' + urlParams;
}