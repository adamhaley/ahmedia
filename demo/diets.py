from pathlib import Path

# Adding more dietary guideline markdown files: Whole30, nut-free, and low-FODMAP
additional_diets = {
 "paleo.md": """### Paleo Diet Guidelines
**Allowed**
- Grass-fed meats, fish, eggs
- Vegetables, fruits
- Nuts and seeds (excluding peanuts)
- Natural oils: olive oil, coconut oil, avocado oil
- Root vegetables (in moderation)
- Cassava, sweet potatoes

**Avoid**
- Grains: wheat, oats, rice, barley, corn
- Legumes: beans, lentils, soy, peanuts
- Dairy products
- Refined sugar and sweeteners
- Processed or packaged foods
""",
    "gluten_free.md": """### Gluten-Free Diet Guidelines

**Allowed**
- Meats, fish, poultry
- Fruits and vegetables
- Gluten-free grains: rice, corn, quinoa, cassava
- Legumes
- Dairy products (if tolerated)
- Gluten-free labeled products

**Avoid**
- Wheat, barley, rye
- Traditional pasta, bread, baked goods (unless GF-labeled)
- Beer (unless gluten-free)
- Most soy sauces (unless GF-labeled)
""",
    "vegan.md": """### Vegan Diet Guidelines

**Allowed**
- All plant-based foods: vegetables, fruits, legumes, grains, nuts, seeds
- Plant-based oils and fats
- Non-dairy milks (almond, oat, soy)
- Tofu, tempeh, seitan (if not restricted)

**Avoid**
- All animal products: meat, poultry, fish, eggs, dairy
- Gelatin, honey, bone broth
- Animal-derived food additives (e.g., casein, whey, lactose)
""",
    "keto.md": """### Keto Diet Guidelines

**Allowed**
- High-fat foods: oils, nuts, seeds, cheese, avocado
- Low-carb vegetables: leafy greens, cauliflower, zucchini
- Meats and seafood
- Eggs
- Butter and cream

**Avoid**
- Sugar and sweeteners (except keto-friendly ones like stevia)
- Grains and starches: bread, pasta, rice, potatoes
- Most fruits (except berries)
- Legumes
- High-carb vegetables: carrots, corn, peas
""",
    "whole30.md": """### Whole30 Diet Guidelines

**Allowed**
- Meat, seafood, eggs
- Vegetables and fruits (in moderation)
- Natural fats and oils (olive, coconut, avocado)
- Nuts and seeds (except peanuts)
- Fresh herbs and spices

**Avoid**
- Added sugar (real or artificial)
- Alcohol
- Grains (wheat, rice, corn, oats, etc.)
- Legumes (beans, lentils, peanuts, soy)
- Dairy products
- Carrageenan, MSG, sulfites
- Baked goods or junk foods made with compliant ingredients
""",
    "nut_free.md": """### Nut-Free Diet Guidelines

**Allowed**
- Fruits and vegetables
- Grains and legumes (if no other restrictions)
- Seeds: pumpkin, sunflower, chia, flax
- Meat, poultry, fish, eggs
- Dairy (if tolerated)
- Nut-free processed foods

**Avoid**
- All tree nuts: almonds, walnuts, cashews, pecans, hazelnuts, pistachios, etc.
- Peanuts (though technically a legume, often included in nut-free restrictions)
- Nut oils and butters (e.g., almond butter, peanut butter)
- Foods processed in facilities that handle nuts (when strict)
""",
    "low_fodmap.md": """### Low FODMAP Diet Guidelines

**Allowed (Low FODMAP)**
- Proteins: plain meat, poultry, fish, eggs
- Most hard cheeses, lactose-free dairy
- Vegetables: carrots, spinach, zucchini, cucumbers, bell peppers
- Fruits: strawberries, blueberries, oranges, grapes
- Grains: rice, oats, quinoa (in moderation)

**Avoid (High FODMAP)**
- Onions, garlic, leeks, shallots
- Legumes: lentils, chickpeas, kidney beans
- Dairy with lactose: milk, soft cheeses, yogurt
- Fruits: apples, pears, watermelon, mango
- Sweeteners: sorbitol, mannitol, xylitol, high-fructose corn syrup
"""
}

# Output directory 
guideline_dir = Path("diets")
guideline_dir.mkdir(parents=True, exist_ok=True)

# Save the additional diet files
for filename, content in additional_diets.items():
    (guideline_dir / filename).write_text(content)

guideline_dir

