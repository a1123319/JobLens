package com.joblens.joblens.entity;

import jakarta.persistence.Entity;
import jakarta.persistence.Id;
import jakarta.persistence.ManyToOne;
import jakarta.persistence.JoinColumn;
import jakarta.persistence.Table;

@Entity
@Table(name = "recruitment")
public class Recruitment {

    @Id
    private Long id;

    @ManyToOne
    @JoinColumn(name = "CompanyId", nullable = false)
    private RecruitmentSource recruitmentSource;

    private String url;
    private String name;
    private String salary;

    // Getters and setters
    public Long getId() {
        return id;
    }

    public void setId(Long id) {
        this.id = id;
    }

    public RecruitmentSource getRecruitmentSource() {
        return recruitmentSource;
    }

    public void setRecruitmentSource(RecruitmentSource recruitmentSource) {
        this.recruitmentSource = recruitmentSource;
    }

    public String getUrl() {
        return url;
    }

    public void setUrl(String url) {
        this.url = url;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public String getSalary() {
        return salary;
    }

    public void setSalary(String salary) {
        this.salary = salary;
    }
}
