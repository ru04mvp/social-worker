echo '===> 增加 Android 平台 ...'
cordova platform add android@7.1.4
echo ''
echo '===> 增加 IOS 平台 ...'
cordova platform add ios
echo ''
echo '===> 複製 google-services.json 到指定位置 ...'
cp google-services.json platforms/android/app/.
echo ''
echo '===> 開始編譯 Android ...'
cordova build android
echo ''
echo '===> 開始編譯 IOS ...'
cordova build ios
