<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Japanese translation of the framework's default validation messages.
    |
    */

    'accepted' => ':attributeを承認してください。',
    'accepted_if' => ':otherが:valueの場合、:attributeを承認してください。',
    'active_url' => ':attributeには有効なURLを指定してください。',
    'after' => ':attributeには:dateより後の日付を指定してください。',
    'after_or_equal' => ':attributeには:date以降の日付を指定してください。',
    'alpha' => ':attributeには英字のみ使用できます。',
    'alpha_dash' => ':attributeには英数字・ハイフン・アンダースコアのみ使用できます。',
    'alpha_num' => ':attributeには英数字のみ使用できます。',
    'any_of' => ':attributeが正しくありません。',
    'array' => ':attributeには配列を指定してください。',
    'array_keys' => ':attributeには次のキーのみ指定できます：:values',
    'ascii' => ':attributeには半角英数字と記号のみ使用できます。',
    'base64' => ':attributeには有効なBase64文字列を指定してください。',
    'before' => ':attributeには:dateより前の日付を指定してください。',
    'before_or_equal' => ':attributeには:date以前の日付を指定してください。',
    'between' => [
        'array' => ':attributeは:min〜:max個にしてください。',
        'file' => ':attributeは:min〜:maxKBのファイルにしてください。',
        'numeric' => ':attributeは:min〜:maxにしてください。',
        'string' => ':attributeは:min〜:max文字にしてください。',
    ],
    'boolean' => ':attributeにはtrueかfalseを指定してください。',
    'can' => ':attributeに許可されていない値が含まれています。',
    'confirmed' => ':attributeが確認用と一致しません。',
    'contains' => ':attributeに必要な値が含まれていません。',
    'current_password' => 'パスワードが正しくありません。',
    'date' => ':attributeには有効な日付を指定してください。',
    'date_equals' => ':attributeには:dateを指定してください。',
    'date_format' => ':attributeは:format形式で指定してください。',
    'decimal' => ':attributeは小数点以下:decimal桁で指定してください。',
    'declined' => ':attributeを拒否してください。',
    'declined_if' => ':otherが:valueの場合、:attributeを拒否してください。',
    'different' => ':attributeと:otherには異なる値を指定してください。',
    'digits' => ':attributeは:digits桁で指定してください。',
    'digits_between' => ':attributeは:min〜:max桁で指定してください。',
    'dimensions' => ':attributeの画像サイズが正しくありません。',
    'distinct' => ':attributeに重複した値があります。',
    'doesnt_contain' => ':attributeに次の値を含めることはできません：:values',
    'doesnt_end_with' => ':attributeは次のいずれかで終わってはいけません：:values',
    'doesnt_start_with' => ':attributeは次のいずれかで始まってはいけません：:values',
    'email' => ':attributeには有効なメールアドレスを指定してください。',
    'encoding' => ':attributeは:encodingでエンコードしてください。',
    'ends_with' => ':attributeは次のいずれかで終わる必要があります：:values',
    'enum' => '選択された:attributeは正しくありません。',
    'exists' => '選択された:attributeは正しくありません。',
    'extensions' => ':attributeには次の拡張子のファイルを指定してください：:values',
    'file' => ':attributeにはファイルを指定してください。',
    'filled' => ':attributeを入力してください。',
    'gt' => [
        'array' => ':attributeは:value個より多くしてください。',
        'file' => ':attributeは:valueKBより大きいファイルにしてください。',
        'numeric' => ':attributeは:valueより大きくしてください。',
        'string' => ':attributeは:value文字より多くしてください。',
    ],
    'gte' => [
        'array' => ':attributeは:value個以上にしてください。',
        'file' => ':attributeは:valueKB以上のファイルにしてください。',
        'numeric' => ':attributeは:value以上にしてください。',
        'string' => ':attributeは:value文字以上にしてください。',
    ],
    'hex_color' => ':attributeには有効な16進数のカラーコードを指定してください。',
    'image' => ':attributeには画像ファイルを指定してください。',
    'in' => '選択された:attributeは正しくありません。',
    'in_array' => ':attributeは:otherに含まれている必要があります。',
    'in_array_keys' => ':attributeには次のキーのいずれかを含めてください：:values',
    'integer' => ':attributeには整数を指定してください。',
    'ip' => ':attributeには有効なIPアドレスを指定してください。',
    'ipv4' => ':attributeには有効なIPv4アドレスを指定してください。',
    'ipv6' => ':attributeには有効なIPv6アドレスを指定してください。',
    'json' => ':attributeには有効なJSON文字列を指定してください。',
    'list' => ':attributeにはリストを指定してください。',
    'lowercase' => ':attributeは小文字で入力してください。',
    'lt' => [
        'array' => ':attributeは:value個より少なくしてください。',
        'file' => ':attributeは:valueKBより小さいファイルにしてください。',
        'numeric' => ':attributeは:valueより小さくしてください。',
        'string' => ':attributeは:value文字より少なくしてください。',
    ],
    'lte' => [
        'array' => ':attributeは:value個以下にしてください。',
        'file' => ':attributeは:valueKB以下のファイルにしてください。',
        'numeric' => ':attributeは:value以下にしてください。',
        'string' => ':attributeは:value文字以下にしてください。',
    ],
    'mac_address' => ':attributeには有効なMACアドレスを指定してください。',
    'max' => [
        'array' => ':attributeは:max個以下にしてください。',
        'file' => ':attributeは:maxKB以下のファイルにしてください。',
        'numeric' => ':attributeは:max以下にしてください。',
        'string' => ':attributeは:max文字以内で入力してください。',
    ],
    'max_digits' => ':attributeは:max桁以下にしてください。',
    'mimes' => ':attributeには次の形式のファイルを指定してください：:values',
    'mimetypes' => ':attributeには次の形式のファイルを指定してください：:values',
    'min' => [
        'array' => ':attributeは:min個以上にしてください。',
        'file' => ':attributeは:minKB以上のファイルにしてください。',
        'numeric' => ':attributeは:min以上にしてください。',
        'string' => ':attributeは:min文字以上で入力してください。',
    ],
    'min_digits' => ':attributeは:min桁以上にしてください。',
    'missing' => ':attributeは指定できません。',
    'missing_if' => ':otherが:valueの場合、:attributeは指定できません。',
    'missing_unless' => ':otherが:valueでない場合、:attributeは指定できません。',
    'missing_with' => ':valuesを指定した場合、:attributeは指定できません。',
    'missing_with_all' => ':valuesをすべて指定した場合、:attributeは指定できません。',
    'multiple_of' => ':attributeには:valueの倍数を指定してください。',
    'not_in' => '選択された:attributeは正しくありません。',
    'not_regex' => ':attributeの形式が正しくありません。',
    'numeric' => ':attributeには数値を指定してください。',
    'password' => [
        'letters' => ':attributeには英字を1文字以上含めてください。',
        'mixed' => ':attributeには大文字と小文字をそれぞれ1文字以上含めてください。',
        'numbers' => ':attributeには数字を1文字以上含めてください。',
        'symbols' => ':attributeには記号を1文字以上含めてください。',
        'uncompromised' => 'この:attributeは過去に漏洩したことがあります。別の:attributeを指定してください。',
    ],
    'present' => ':attributeが存在している必要があります。',
    'present_if' => ':otherが:valueの場合、:attributeが存在している必要があります。',
    'present_unless' => ':otherが:valueでない場合、:attributeが存在している必要があります。',
    'present_with' => ':valuesを指定した場合、:attributeが存在している必要があります。',
    'present_with_all' => ':valuesをすべて指定した場合、:attributeが存在している必要があります。',
    'prohibited' => ':attributeは指定できません。',
    'prohibited_if' => ':otherが:valueの場合、:attributeは指定できません。',
    'prohibited_if_accepted' => ':otherを承認した場合、:attributeは指定できません。',
    'prohibited_if_declined' => ':otherを拒否した場合、:attributeは指定できません。',
    'prohibited_unless' => ':otherが:valuesのいずれかでない場合、:attributeは指定できません。',
    'prohibits' => ':attributeを指定した場合、:otherは指定できません。',
    'regex' => ':attributeの形式が正しくありません。',
    'required' => ':attributeを入力してください。',
    'required_array_keys' => ':attributeには次の項目を含めてください：:values',
    'required_if' => ':otherが:valueの場合、:attributeを入力してください。',
    'required_if_accepted' => ':otherを承認した場合、:attributeを入力してください。',
    'required_if_declined' => ':otherを拒否した場合、:attributeを入力してください。',
    'required_unless' => ':otherが:valuesのいずれかでない場合、:attributeを入力してください。',
    'required_with' => ':valuesを指定した場合、:attributeを入力してください。',
    'required_with_all' => ':valuesをすべて指定した場合、:attributeを入力してください。',
    'required_without' => ':valuesを指定しない場合、:attributeを入力してください。',
    'required_without_all' => ':valuesをどれも指定しない場合、:attributeを入力してください。',
    'same' => ':attributeと:otherが一致しません。',
    'size' => [
        'array' => ':attributeは:size個にしてください。',
        'file' => ':attributeは:sizeKBのファイルにしてください。',
        'numeric' => ':attributeは:sizeにしてください。',
        'string' => ':attributeは:size文字にしてください。',
    ],
    'starts_with' => ':attributeは次のいずれかで始まる必要があります：:values',
    'string' => ':attributeには文字列を指定してください。',
    'timezone' => ':attributeには有効なタイムゾーンを指定してください。',
    'unique' => 'この:attributeは既に使われています。',
    'uploaded' => ':attributeのアップロードに失敗しました。',
    'uppercase' => ':attributeは大文字で入力してください。',
    'url' => ':attributeには有効なURLを指定してください。',
    'ulid' => ':attributeには有効なULIDを指定してください。',
    'uuid' => ':attributeには有効なUUIDを指定してください。',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    */

    'custom' => [
        'slug' => [
            'regex' => ':attributeは英小文字・数字・ハイフンで入力してください（例：my-first-post）。',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | Field names shared across forms. A FormRequest's attributes() method
    | overrides these for its own fields.
    |
    */

    'attributes' => [
        'name' => '名前',
        'email' => 'メールアドレス',
        'password' => 'パスワード',
        'current_password' => '現在のパスワード',
        'password_confirmation' => 'パスワード（確認用）',
        'title' => 'タイトル',
        'body' => '本文',
        'description' => '説明',
        'demo_url' => '確認用URL',
        'github_url' => 'GitHub URL',
        'slug' => 'スラッグ',
        'status' => '公開状態',
        'published_at' => '公開日',
        'category_id' => 'カテゴリ',
        'tags' => 'タグ',
        'thumbnail' => 'サムネイル画像',
        'images' => 'ギャラリー画像',
    ],

];
