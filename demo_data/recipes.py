from pathlib import Path

# Sample demo recipes in Markdown format with frontmatter-style metadata
recipes = [
    {
        "filename": "wild_mushroom_cassava_tacos.md",
        "title": "Wild Mushroom Cassava Tacos",
        "ingredients": [
            "sautéed lion’s mane mushrooms",
            "shiitake mushrooms",
            "cassava tortillas",
            "tahini",
            "Kashmiri chili blend",
            "red spring onions",
            "broccoli",
            "baby bok choy"
        ],
        "dietary_tags": ["vegetarian", "paleo", "gluten-free", "nut-free"],
        "notes": "Contains sesame (tahini). Can be made Whole30 by omitting tahini."
    },
    {
        "filename": "seared_duck_with_fig_reduction.md",
        "title": "Seared Duck with Fig Reduction",
        "ingredients": [
            "duck breast",
            "black mission figs",
            "balsamic vinegar",
            "rosemary",
            "garlic",
            "sea salt",
            "olive oil"
        ],
        "dietary_tags": ["paleo", "keto", "gluten-free", "dairy-free"],
        "notes": "Not vegetarian. Garlic may need to be excluded for low-FODMAP guests."
    },
    {
        "filename": "cashew_cream_zoodles.md",
        "title": "Cashew Cream Zoodles",
        "ingredients": [
            "zucchini noodles",
            "cashews",
            "nutritional yeast",
            "lemon juice",
            "black pepper",
            "sea salt"
        ],
        "dietary_tags": ["vegan", "gluten-free", "dairy-free"],
        "notes": "Not nut-free. High in healthy fats."
    },
    {
        "filename": "smoked_beet_carpaccio.md",
        "title": "Smoked Beet Carpaccio",
        "ingredients": [
            "beets",
            "smoked salt",
            "arugula",
            "toasted pumpkin seeds",
            "olive oil",
            "balsamic reduction"
        ],
        "dietary_tags": ["vegan", "paleo", "nut-free", "gluten-free"],
        "notes": "Allergens: none. A crowd favorite among guests with dietary restrictions."
    }
]

# Output directory
output_dir = Path("recipes")
output_dir.mkdir(parents=True, exist_ok=True)

# Save each recipe as a markdown file
for recipe in recipes:
    content = f"""---
title: {recipe['title']}
ingredients:
{chr(10).join(f"  - {i}" for i in recipe['ingredients'])}
dietary_tags:
{chr(10).join(f"  - {t}" for t in recipe['dietary_tags'])}
notes: {recipe['notes']}
---

This exclusive dish is crafted with precision and only served at our chef’s table.
"""
    (output_dir / recipe["filename"]).write_text(content)

output_dir

