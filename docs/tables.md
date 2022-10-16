Databaseのtable設定（サービス用）の管理システム
==

# Summary
- Databaseに登録するデータの仕様を管理画面で構築するシステム
- 

# Specification
- 保存階層 : data/%database名/tables/***.json
- 「lib/data/tables」はシステム専用tableでサービス側での設定変更は不可
- 読み込み優先度は、サービスtable > システムtableで速度向上を優先する
- システムtableと同じ名称のtable名は登録できない。

# 
