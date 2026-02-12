package co.shop;

import android.os.Bundle;
import android.support.v7.app.AppCompatActivity;
import android.view.View;
import android.webkit.WebView;
import android.widget.RelativeLayout;

public class UrlActivity extends AppCompatActivity {

    private WebView mWebView;

    private String m_strUrl = "";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_url);

        m_strUrl = getIntent().getStringExtra("url");

        initUI();
    }

    private void initUI() {
        mWebView = findViewById(R.id.webview);
        mWebView.loadUrl(m_strUrl);

        ((RelativeLayout) findViewById(R.id.rly_back)).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                finish();
            }
        });
    }
}