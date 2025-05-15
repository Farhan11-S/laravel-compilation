<?php

namespace App\Enums;

enum PremiumAdPlace: string
{
    case FIRST_JOB_HOMEPAGE = 'first_job_homepage';
    case BETWEEN_JOBS_HOMEPAGE = 'between_jobs_homepage';
    case JOB_DETAIL_PAGE = 'job_detail_page';
    case BLOG_LIST_PAGE = 'blog_list_page';
    case BLOG_DETAIL_PAGE = 'blog_detail_page';
}
