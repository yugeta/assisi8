RSS
==

# reference

https://developers.google.com/search/reference/podcast/rss-feed?hl=ja
https://support.brightcove.com/ja/cms-api-sample-generate-rss-podcast-feed-itunes

# podcast仕様

```
<?xml version="1.0" encoding="UTF-8" ?>
<?xml-stylesheet href="{{rss_url}}" type="text/xsl" ?>
<rss version="2.0"
     xmlns:itunes="http://www.itunes.com/DTDs/Podcast-1.0.dtd"
     xmlns:googleplay="http://www.google.com/schemas/play-podcasts/1.0">
  <channel>
    <title>{{title}}</title>
    <link>{{url}}</link>
    <description>{{description}}</description>
    <language>ja</language>
    <docs>{{docs}}</docs>

    <itunes:author>{{author}}</itunes:author>
    <itunes:image href="{{banner}}"/>
    <itunes:owner>
      <itunes:email>{{email}}</itunes:email>
      <itunes:name>{{author}}</itunes:name>
    </itunes:owner>
    <itunes:summary>{{description}}</itunes:summary>
    <itunes:category text="{{category}}">
      <itunes:category text="{{category_sub}}" />
    </itunes:category>

    <item>
      <title>{{program_title}}</title>
      <link>{{program_url}}</link>
      <enclosure url="{{program_mp3}}" length="" type="audio/mpeg"/>
      <pubDate>{{program_date}}</pubDate>
      <itunes:author>{{author}}</itunes:author>
      <description>{{prigram_description}}</description>
      <guid>{{program_mp3}}</guid>
    </item>

    ...

  </channel>
</rss>

```

