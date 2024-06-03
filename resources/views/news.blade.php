@extends('layouts.main')

@section('title', 'News Page')
@section('meta_description', 'This is the news page description')
@section('meta_keywords', 'news, updates, articles')
@section('meta_image', asset('images/news-page-image.jpg'))

@section('structured_data')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "NewsArticle",
    "mainEntityOfPage": {
        "@type": "WebPage",
        "@id": "https://www.bachhoaxanh.com/kinh-nghiem-hay/thong-bao-chinh-sach-hang-thanh-vien-moi-2024-1564161"
    },
    "headline": "Thông báo chính sách hạng thành viên mới 2024 của Bách hóa XANH",
    "image": {
        "@type": "ImageObject",
        "url": "https://www.bachhoaxanh.com/images/2024/04/03/1564161/thong-bao-chinh-sach-hang-thanh-vien-moi-2024-cua-bach-hoa-xanh-202404031348329042.jpg",
        "width": 760,
        "height": 442
    },
    "description": "Bách hóa XANH hân hạnh thông báo chính sách hạng thành viên mới 2024, áp dụng từ ngày 1/4/2024 dành cho khách hàng mua hàng và tích điểm tại Bách hóa XANH.",
    "name": "Thông báo chính sách hạng thành viên mới 2024 của Bách hóa XANH",
    "inLanguage": "vi-vn",
    "datePublished": "2024-04-03T11:09:48",
    "dateModified": "2024-04-03T11:09:47",
    "author": {
        "@type": "Person",
        "name": "Jolie",
        "url": "https://www.bachhoaxanh.com/kinh-nghiem-hay/thong-bao-chinh-sach-hang-thanh-vien-moi-2024-1564161#author",
        "memberOf": {
            "@id": "https://www.bachhoaxanh.com/#Organization"
        }
    },
    "publisher": {
        "@id": "https://www.bachhoaxanh.com/#Organization"
    }
}
</script>
@endsection

@section('content')
    <h1>News Page</h1>
    <p>This is the news page.</p>
@endsection
