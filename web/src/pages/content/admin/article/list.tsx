import React, { useRef } from 'react'
import { useTranslate, useDelete, useDeleteMany, useNavigation } from '@refinedev/core'
import { PrimaryTableCol, Button, Input, Tag, Link, Popconfirm } from 'tdesign-react/esm'
import { PageTable, FilterItem, MediaText, CascaderAsync, TableRef } from '@duxweb/dux-refine'

const List = () => {
  const translate = useTranslate()
  const { mutate } = useDelete()
  const { mutate: deleteMany } = useDeleteMany()
  const { create, edit } = useNavigation()

  const columns = React.useMemo<PrimaryTableCol[]>(
    () => [
      { colKey: 'row-select', type: 'multiple' },
      {
        colKey: 'id',
        sorter: true,
        sortType: 'all',
        title: 'ID',
        width: 100,
      },
      {
        colKey: 'title',
        title: translate('content.article.fields.title'),
        minWidth: 200,
        cell: ({ row }) => {
          return (
            <MediaText size='small'>
              {row.images?.[0] && <MediaText.Image src={row.images}></MediaText.Image>}
              <MediaText.Title>{row.title}</MediaText.Title>
              <MediaText.Desc>{row.subtitle}</MediaText.Desc>
            </MediaText>
          )
        },
      },
      {
        colKey: 'class_name',
        title: translate('content.article.fields.category'),
        width: 200,
        cell: ({ row }) => {
          return row.class_name?.join(' > ')
        },
      },
      {
        colKey: 'source',
        title: translate('content.article.fields.source'),
        width: 200,
      },
      {
        colKey: 'status',
        title: translate('content.article.fields.status'),
        width: 150,
        filter: {
          type: 'single',
          list: [
            { label: translate('content.article.tab.published'), value: '1' },
            { label: translate('content.article.tab.unpublished'), value: '2' },
          ],
        },
        cell: ({ row }) => {
          return (
            <>
              {row.status ? (
                <Tag theme='warning' variant='outline'>
                  {translate('content.article.tab.published')}
                </Tag>
              ) : (
                <Tag theme='success' variant='outline'>
                  {translate('content.article.tab.unpublished')}
                </Tag>
              )}
            </>
          )
        },
      },
      {
        colKey: 'created_at',
        title: translate('content.article.fields.createdAt'),
        sorter: true,
        sortType: 'all',
        width: 200,
      },
      {
        colKey: 'link',
        title: translate('table.actions'),
        fixed: 'right',
        align: 'center',
        width: 120,
        cell: ({ row }) => {
          return (
            <div className='flex justify-center gap-4'>
              <Link theme='primary' onClick={() => edit('content.article', row.id)}>
                {translate('buttons.edit')}
              </Link>
              <Popconfirm
                content={translate('buttons.confirm')}
                destroyOnClose
                placement='top'
                showArrow
                theme='default'
                onConfirm={() => {
                  mutate({
                    resource: 'content.article',
                    id: row.id,
                  })
                }}
              >
                <Link theme='danger'>{translate('buttons.delete')}</Link>
              </Popconfirm>
            </div>
          )
        },
      },
    ],
    // eslint-disable-next-line react-hooks/exhaustive-deps
    [translate],
  )

  const table = useRef<TableRef>(null)

  return (
    <PageTable
      ref={table}
      columns={columns}
      tabs={[
        {
          label: translate('content.article.tab.all'),
          value: '0',
        },
        {
          label: translate('content.article.tab.published'),
          value: '1',
        },
        {
          label: translate('content.article.tab.unpublished'),
          value: '2',
        },
      ]}
      batchRender={() => (
        <>
          <Popconfirm
            content={translate('buttons.confirm')}
            destroyOnClose
            placement='top'
            showArrow
            theme='default'
            onConfirm={() => {
              deleteMany({
                resource: 'content.article',
                ids: table.current?.selecteds || [],
              })
            }}
          >
            <Button
              variant='outline'
              theme='danger'
              icon={<div className='i-tabler:trash t-icon'></div>}
            >
              {translate('buttons.delete')}
            </Button>
          </Popconfirm>
        </>
      )}
      actionRender={() => (
        <Button
          icon={<div className='t-icon i-tabler:plus'></div>}
          onClick={() => {
            create('content.article')
          }}
        >
          {translate('buttons.create')}
        </Button>
      )}
      filterRender={() => {
        return (
          <>
            <FilterItem name='keyword'>
              <Input placeholder={translate('content.article.validate.title')} />
            </FilterItem>
            <FilterItem name='class_id'>
              <CascaderAsync
                placeholder={translate('content.article.validate.class')}
                url='content/category'
                keys={{
                  label: 'name',
                  value: 'id',
                }}
                format={(v) => parseInt(v)}
                filterable
                clearable
              />
            </FilterItem>
          </>
        )
      }}
    />
  )
}

export default List
