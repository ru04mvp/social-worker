# ======
echo ""
echo "===> 移除 Andoird 現有的檔案資源"
rm -rf ./platforms/android/app/src/main/assets/www/resources
rm -rf ./platforms/android/app/src/main/assets/www/index.html
# ======
# echo ""
# echo "===> 移除 IOS 現有的檔案資源"
# rm -rf ./platforms/ios/www/resources
# rm -rf ./platforms/ios/www/index.html
# ======
cd develope
npm run build
cd ..
# ======
echo ""
echo "===> 更新 Andoird 現有的檔案資源"
cp -rf ./www/* ./platforms/android/app/src/main/assets/www/.
# echo ""
# echo "===> 更新 IOS 現有的檔案資源"
# cp -rf ./www/* ./platforms/ios/www/.