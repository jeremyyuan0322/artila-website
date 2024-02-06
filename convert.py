# Correcting the approach to ensure accurate parsing and YAML generation, avoiding previous errors.

def parse_and_generate_yaml(txt_content):
    # Splitting content into lines
    lines = txt_content.split("\n")
    
    # Preparing the YAML structure
    yaml_data = {
        "part1-page": {},
        "part2-files": {},
        "part3-features": {"title": "Features", "details": []},
        "part4-datasheet": {"datasheet": [], "hw_specifications": {}, "sw_specifications": {}, "ordering_information": {}}
    }
    current_section = ""
    datasheet_section = ""
    features_list = []
    hw_spec_list = []
    sw_spec_list = []
    ordering_list = []
    
    # Parsing the text content
    for line in lines:
        if line.startswith("#part1-page"):
            current_section = "part1"
        elif line.startswith("#part2-files"):
            current_section = "part2"
        elif line.startswith("#part3-features"):
            current_section = "part3"
        elif line.startswith("#part4-datasheet"):
            current_section = "part4"
            yaml_data["part4-datasheet"]["datasheet"] = [section.strip() for section in line.split("-") if section.isdigit()]
        else:
            if current_section == "part1" and ":" in line:
                key, value = line.split(":", 1)
                yaml_data["part1-page"][key.strip()] = f'"{value.strip()}"'
            elif current_section == "part3" and line.strip().startswith("•"):
                features_list.append(f'"{line.strip().lstrip("•").strip()}"')
            elif current_section == "part4" and line.strip().startswith("•"):
                if "H/W Specifications" in datasheet_section:
                    hw_spec_list.append(f'"{line.strip().lstrip("•").strip()}"')
                elif "S/W Specifications" in datasheet_section:
                    sw_spec_list.append(f'"{line.strip().lstrip("•").strip()}"')
                elif "Ordering Information" in datasheet_section:
                    ordering_list.append(f'"{line.strip().lstrip("•").strip()}"')
            elif current_section == "part4":
                if any(header in line for header in ["H/W Specifications", "S/W Specifications", "Ordering Information"]):
                    datasheet_section = line.strip()

    # Assigning the parsed content to the YAML structure
    yaml_data["part3-features"]["details"] = features_list
    yaml_data["part4-datasheet"]["hw_specifications"]["title"] = '"H/W Specifications"'
    yaml_data["part4-datasheet"]["hw_specifications"]["sections"] = {"details": hw_spec_list}
    yaml_data["part4-datasheet"]["sw_specifications"]["title"] = '"S/W Specifications"'
    yaml_data["part4-datasheet"]["sw_specifications"]["sections"] = {"details": sw_spec_list}
    yaml_data["part4-datasheet"]["ordering_information"]["title"] = '"Ordering Information"'
    yaml_data["part4-datasheet"]["ordering_information"]["sections"] = {"details": ordering_list}
    
    # Adding files paths based on the product
    product = yaml_data["part1-page"].get("product", "").strip('"')
    yaml_data["part2-files"] = {
        "Data Sheet": f'"docs/{product}/{product}_Datasheet.pdf"',
        "Hardware Guide": f'"docs/{product}/{product}_Hardware_Guide.pdf"',
        "Software Guide": f'"docs/{product}/{product}_Software_Guide.pdf"'
    }

    # Generating YAML string
    yaml_str = yaml.dump(yaml_data, allow_unicode=True, sort_keys=False, default_flow_style=False, indent=2)
    return yaml_str

# Attempting to generate the YAML content correctly this time
corrected_yaml_content_final = parse_and_generate_yaml(datasheet_txt_content)
print(corrected_yaml_content_final)
