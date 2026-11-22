@props([
    'id'           => 1,
    'venue'        => null,
    'venueName'    => null,
    'name'         => 'Nama Lapangan',
    'fieldName'    => null,
    'sport'        => 'Futsal',
    'courtType'    => null,
    'city'         => 'Jakarta',
    'location'     => null,
    'rating'       => '4.8',
    'reviews'      => 100,
    'reviewsCount' => null,
    'price'        => 100000,
    'pricePerHour' => null,
    'facilities'   => [],
    'openHours'    => '08:00 - 23:00',
    'image'        => null,
    'imageSrc'     => null,
    'href'         => null,
    'bookingUrl'   => null,
])

<x-penyewa.court.card
    :id="$id"
    :venue-name="$venueName ?? $venue ?? $name"
    :field-name="$fieldName ?? $name"
    :sport="$sport"
    :court-type="$courtType ?? $fieldName ?? $name"
    :location="$location ?? $city"
    :rating="$rating"
    :reviews-count="(int) ($reviewsCount ?? $reviews)"
    :price-per-hour="(int) ($pricePerHour ?? $price)"
    :facilities="$facilities"
    :open-hours="$openHours"
    :image-src="$imageSrc ?? $image"
    :booking-url="$bookingUrl ?? $href"
/>
