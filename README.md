# EC-CUBE 4.0 VI Version

## Cách cài đặt EC-CUBE 4.0

Vui lòng cài đặt theo quy trình về [cách cài đặt](http://docs.ec-cube.vn/?p=253) ở tài liệu phát triển.

## Cách chỉnh sửa / xây dựng CSS

Nó được mô tả bằng [Sass](http://sass-lang.com).
Mã nguồn Sass được đặt trong `html/template/{admin,default}/assets/scss`.
Vui lòng cài đặt [nodejs](https://nodejs.org/en/) để chạy Sass

Cách chạy nodejs

```shell
npm install
npm run build
```

## Môi trường xác nhận hoạt động

* Apache/2.4.x (mod_rewrite / mod_ssl)
* PHP7.1.20
* PostgreSQL 9.2.1   

Vui lòng kiểm tra các [yêu cầu hệ thống](http://docs.ec-cube.vn/?p=80) của tài liệu phát triển để biết chi tiết.

## Tài liệu
http://docs.ec-cube.vn/

## Cộng đồng FB
https://www.facebook.com/groups/eccube.vn/

# Git Flow Explain

Since we are based on ECCUBE-JP, we try to define a flow to easy develop and contribute to ECCUBE-JP. 
There are 3 branches:
 - `onshop/develop` 
	 - This is develop branch which contain latest commits.
 - `onshop/master` 
	 - This is stable branch which used to release each version.
 - `onshop/4.0`
	 - This is contribute branch which used to track PR to ECCUBE-JP

## Use Cases

To help you get familiar with our git flow, we try to explain what/how to do in common use cases.
### UC1: Upstream latest ECCUBE-JP release
1. Checkout to latest release of ECCUBE-JP `git checkout 4.0.3` (4.0.3 is latest release at this time)
2. Create a branch from it called `feature-upstream-4.0.3/onshop/4.0`
3. Create pull request from `feature-upstream-4.0.3/onshop/4.0` to `onshop/4.0`
> **Note:** Remember branch `onshop/4.0` contains **latest code of latest release** of ECCUBE-JP, not **latest code**.

### UC2: Add new feature / fix a bug to ECCUBE-JP

1. Create a branch from `onshop/4.0` called `feature-new/onshop/4.0`
2. Commit your code into branch `feature-new/onshop/4.0`
3. Create a pull request from `feature-new/onshop/4.0` to `ECCUBE-JP (https://github.com/EC-CUBE/ec-cube)`
4. Create a pull request from `feature-new/onshop/4.0` to `onshop/develop` 
> **Note:** Remember branch `onshop/develop` need contain latest code so need step 4 to make sure it.

### UC3: Contribute to ECCUBE-JP
1. Create a pull request from `feature-new/onshop/4.0` to `ECCUBE-JP (https://github.com/EC-CUBE/ec-cube)`

### UC4: Add new feature/ fix a bug to ONSHOP 
1. Create a branch from `onshop/master` called `feature-new/onshop/master`
2. Commit your code into branch `feature-new/onshop/master`
3. Create a pull request from `feature-new/onshop/master` to `onshop/develop`

### UC5: Release a new version of ONSHOP
1. Create pull requests from features/bugs which decided release in this version.
	 > Create a pull request from `feature-new-1/onshop/master` to `onshop/master`
	 > Create a pull request from `feature-new-2/onshop/master` to `onshop/master`
	 > Create a pull request from `bug-1/onshop/master` to `onshop/master`
	 > Create a pull request from `bug-2/onshop/master` to `onshop/master`
2. Create a pull request from latest release of ECCUBE-JP (If needed) 
	 > Create a pull request from `onshop/4.0` to `onshop/master`
> **Note:** Need decide features/bugs to release carefully, make sure depend code on each feature. 	 
