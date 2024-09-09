<?php


$excel->setActiveSheet('All Participants');

$excel->addRow([
    'Entry',
    'Bib',
    'Family Name',
    'Given Name',
    'Gender',
    'Birthdate',
    'Category',
    'Country Code',
    'Country Name',
    'Target',
]);//, 'header');

$q=safe_r_sql("select 
        EnCode, 
        coalesce(EdExtra, '') as Bib, 
        EnFirstName,
        EnName,
        if(EnSex=0, 'M', 'W') as Gender,
        if(EnDob>0, EnDob, '') as Birthdate,
        concat(EnDivision,EnClass) as Category,
        CoCode,
        CoName
    from Entries
    inner join Countries on CoId=EnCountry
    left join Qualifications on QuId=EnId
    left join ExtraData on EdId=EnId and EdType='Z'
    where EnTournament={$_SESSION['TourId']}
    order by EnFirstName, EnName");

// add each row
while($r=safe_fetch_assoc($q)) {
    $excel->addRow(array_values($r));
}