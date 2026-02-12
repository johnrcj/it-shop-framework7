package kr.co.conpang.util;

import android.Manifest;
import android.annotation.SuppressLint;
import android.app.Activity;
import android.app.ProgressDialog;
import android.content.ActivityNotFoundException;
import android.content.Context;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.os.AsyncTask;
import android.support.v4.app.ActivityCompat;
import android.support.v4.content.ContextCompat;
import android.support.v4.content.FileProvider;
import android.webkit.MimeTypeMap;
import android.widget.Toast;

import java.io.BufferedInputStream;
import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;
import java.net.MalformedURLException;
import java.net.URL;
import java.net.URLConnection;

import kr.co.conpang.Constants;

public class Downloader {

    public interface DownloadListener {
        void success();
    }

    public static final int PERMISSION_WRITE_EXTERNAL_STORAGE = 0x3000;

    private Context mContext;
    private String dstFilePath;     // 다운로드되는 파일경로
    private String url;             // 다운로드하려는 파일 URL
    private boolean openPath;       // 다운로드후 폴더를 열겠는지를 결정
    private DownloadListener downloadListener;

    public Downloader(Context context) {
        this.mContext = context;
    }

    public void setInfo(String dstPath, String url, boolean openPath) {
        this.dstFilePath = dstPath;
        this.url = url;
        this.openPath = openPath;
    }

    public void setDownloadListener(DownloadListener listener) {
        downloadListener = listener;
    }

    public void startDownload() {
        if (dstFilePath.isEmpty() || url.isEmpty()) {
            return;
        }

        // 외부저장소에 대한 쓰기권한 체크
        if (ContextCompat.checkSelfPermission(mContext, Manifest.permission.WRITE_EXTERNAL_STORAGE) != PackageManager.PERMISSION_GRANTED) {
            ActivityCompat.requestPermissions((Activity) mContext, new String[]{Manifest.permission.WRITE_EXTERNAL_STORAGE}, PERMISSION_WRITE_EXTERNAL_STORAGE);
            return;
        }

        if (!url.startsWith("http://") && !url.startsWith("https://")) {
            url = "http://" + url;
        }

        DownloadFile downloadFile = new DownloadFile();
        downloadFile.execute(url, dstFilePath);
    }

    @SuppressLint("StaticFieldLeak")
    class DownloadFile extends AsyncTask<String, Integer, String> {
        ProgressDialog dlg;

        @Override
        protected String doInBackground(String... strings) {
            try {
                URL url = new URL(strings[0]);
                String dstFolderPath = strings[1];

                URLConnection connection = url.openConnection();
                connection.connect();

                int fileLen = connection.getContentLength();
                InputStream input = new BufferedInputStream(url.openStream());
                OutputStream output = new FileOutputStream(dstFolderPath);

                byte data[] = new byte[1024];
                long total = 0;
                int count;
                while ((count = input.read(data)) != -1) {
                    total += count;

                    publishProgress((int) (total * 100 / fileLen));
                    output.write(data, 0, count);
                }

                output.flush();
                output.close();
                input.close();
            } catch (MalformedURLException e) {
                e.printStackTrace();
            } catch (IOException e) {
                e.printStackTrace();
            }

            return null;
        }

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
            dlg = new ProgressDialog(mContext);
            dlg.setMessage("다운로드중입니다.");
            dlg.setCancelable(false);
            dlg.setCanceledOnTouchOutside(false);
            dlg.setIndeterminate(false);
            dlg.setMax(100);
            dlg.setProgressStyle(ProgressDialog.STYLE_HORIZONTAL);
            dlg.show();
        }

        @Override
        protected void onProgressUpdate(Integer... values) {
            super.onProgressUpdate(values);
            dlg.setProgress(values[0]);
        }

        @Override
        protected void onPostExecute(String s) {
            super.onPostExecute(s);

            dlg.dismiss();
            dlg = null;

            if (openPath) {
                File file = new File(dstFilePath);
                MimeTypeMap map = MimeTypeMap.getSingleton();

                String ext = MimeTypeMap.getFileExtensionFromUrl(file.getName());
                if (ext.isEmpty()) {
                    ext = file.getName().substring(file.getName().lastIndexOf(".") + 1);
                }
                String type = map.getMimeTypeFromExtension(ext);
                if (type == null) {
                    return;
                }

                try {
                    Intent intent = new Intent(Intent.ACTION_VIEW);
                    intent.setDataAndType(FileProvider.getUriForFile(mContext, mContext.getApplicationContext().getPackageName() + ".provider", file), type);
                    intent.addFlags(Intent.FLAG_GRANT_READ_URI_PERMISSION);
                    mContext.startActivity(intent);
                } catch (ActivityNotFoundException e) {
                    e.printStackTrace();
                    Toast.makeText(mContext, Constants.SDCARD_FOLDER + "폴더에 다운로드되었습니다.", Toast.LENGTH_SHORT).show();
                    downloadListener.success();
                }
            } else {
                Toast.makeText(mContext, "다운로드되었습니다.", Toast.LENGTH_SHORT).show();
                downloadListener.success();
            }
        }
    }
}