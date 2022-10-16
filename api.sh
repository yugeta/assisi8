#!/bin/bash

# [sample]
# bash api.sh -p /page/crawl/sh/proc.sh
# -p : path
# -a : api
#
# - 郵便番号データ取得
# bash api.sh -p page/crawl/genre/postal/getCode.sh 
# 
# - smful動画作成バッチ起動
# sh api.sh -p api/smful/api.sh
# 



ROOT=`dirname $0`
cd $ROOT

# ファイル指定(argv)がある場合
while getopts f:d:m:p:a:c: OPT
do
  case $OPT in
    # 格納ディレクトリ [ (d)vendor]
    d ) dir="$OPTARG";;
    # 設定ファイル [ (d)vendor.json , *.json , *.csv]
    f ) filename="$OPTARG";;
    # モード [ (d)auto , check ]
    m ) mode="$OPTARG";;

    # Path
    p ) path="$OPTARG";;

    # api-momdule
    a ) api="$OPTARG";;

    # command直接実行
    c ) command="$OPTARG";;
  esac
done


# コマンド直接実行
if [ "${command}" != "" ];then
  RES=`${command}`
  echo ${RES}
  # echo "Command!! ${command}"
  exit 0
# 階層チェック
elif [ "${api}" != "" ];then
  echo "api : "${api}
  `bash api/${api}/api.sh`
  exit 0

# ファイル実行
elif [ -e ${path} ];then
  echo "path : "$path
  # sh_txt=`cat $path`
  # `echo $sh_txt`
  RES=`bash ${path}`
  echo $RES
  exit 0

# Error
else
  echo "Error !"
  exit 0
fi

