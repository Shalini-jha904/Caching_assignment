Caching Assignment

# Overview

This module provides the following custom blocks:
Last Three Articles Block: Displays the titles of the last 3 articles created on the site as an item list, with caching implemented to ensure the block is automatically invalidated when any of the article titles are updated.

Current User Email Block: Displays the email address of the current user.

Recent Articles by Tag Block: Displays a list of recent articles based on the tag field of the current user, with cache tags and context implemented to achieve this functionality.

## Installation

Download or clone the module into your Drupal site's modules directory.
Navigate to the Extend page (/admin/modules) and enable the custom module.

## Configuration

Last Three Articles Block
This block displays the titles of the last 3 articles created on the site. Caching is implemented to ensure that the block is invalidated automatically when any article title is updated.
Go to the Block layout page (/admin/structure/block).
Place the "Last Three Articles Block" in the desired region.

Current User Email Block
This block displays the email address of the current user.
Go to the Block layout page (/admin/structure/block).
Place the "Current User Email Block" in the desired region.

Recent Articles by Tag Block
This block displays a list of recent articles based on the tag field of the current user. Cache tags and context are implemented to ensure the block updates based on the current user's tags.
Go to the Block layout page (/admin/structure/block).
Place the "Recent Articles by Tag Block" in the desired region.

## Implementation Details

Last Three Articles Block
Uses an entity query to fetch the last 3 articles.
Implements cache tags to ensure the block is invalidated when any article is updated.

Current User Email Block
Retrieves the current user's email address.
Caches the block based on user context.

Recent Articles by Tag Block
Fetches articles based on the tags associated with the current user.
Implements cache tags and cache context to ensure the block updates dynamically based on the current user's tags.
