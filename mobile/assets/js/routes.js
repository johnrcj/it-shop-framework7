let routes = [
    // Default route (404 page). MUST BE THE LAST
    // {
    //     path: '(.*)',
    //     url: './pages/404.html',
    // },
    {
        path: '/login',
        componentUrl: SITE_URL + 'application/views/login/login.html',
    },
    {
        path: '/signup',
        componentUrl: SITE_URL + 'application/views/login/signup.html',
    },
    {
        path: '/signup_term',
        componentUrl: SITE_URL + 'application/views/login/singnup_term.html',
    },
    {
        path: '/find_pwd',
        componentUrl: SITE_URL + 'application/views/login/find_pwd.html',
    },
    {
        path: '/find_pwd_reset',
        componentUrl: SITE_URL + 'application/views/login/find_pwd_reset.html',
    },
    {
        path: '/find_id',
        componentUrl: SITE_URL + 'application/views/login/find_id.html',
    },
    {
        path: '/find_id_complete',
        componentUrl: SITE_URL + 'application/views/login/find_id_complete.html',
    },
    {
        path: '/home',
        componentUrl: SITE_URL + 'application/views/home.html',
    },
    {
        path: '/main',
        componentUrl: SITE_URL + 'application/views/main/main.html',
    },
    {
        path: '/main/search',
        componentUrl: SITE_URL + 'application/views/main/search.html',
    },
    {
        path: '/main/voucher_edit',
        componentUrl: SITE_URL + 'application/views/main/voucher_edit.html',
    },
    {
        path: '/main/detail',
        componentUrl: SITE_URL + 'application/views/main/voucher_detail.html',
    },
    {
        path: '/main/auto_reg',
        componentUrl: SITE_URL + 'application/views/main/voucher_auto_reg.html',
    },
    {
        path: '/refund',
        componentUrl: SITE_URL + 'application/views/refund/refund.html',
    },
    {
        path: '/refund_term',
        componentUrl: SITE_URL + 'application/views/refund/refund_term.html',
    },
    {
        path: '/refund/my_voucher',
        componentUrl: SITE_URL + 'application/views/refund/my_voucher.html',
    },
    {
        path: '/refund/account_select',
        componentUrl: SITE_URL + 'application/views/refund/account_select.html',
    },
    {
        path: '/refund/settlement',
        componentUrl: SITE_URL + 'application/views/refund/settlement.html',
    },
    {
        path: '/mypage',
        componentUrl: SITE_URL + 'application/views/mypage/mypage.html',
    },
    {
        path: '/mypage/info_modify',
        componentUrl: SITE_URL + 'application/views/mypage/info_modify.html',
    },
    {
        path: '/mypage/alarm',
        componentUrl: SITE_URL + 'application/views/mypage/alarm.html',
    },
    {
        path: '/mypage/notice',
        componentUrl: SITE_URL + 'application/views/mypage/notice.html',
    },
    {
        path: '/mypage/useterm',
        componentUrl: SITE_URL + 'application/views/mypage/useterm.html',
    },
    {
        path: '/mypage/notice',
        componentUrl: SITE_URL + 'application/views/mypage/notice.html',
    },
    {
        path: '/mypage/inquiry',
        componentUrl: SITE_URL + 'application/views/mypage/qna.html',
    },
    {
        path: '/mypage/inquiry_write',
        componentUrl: SITE_URL + 'application/views/mypage/qna_write.html',
    },

];